<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Model\Entity\Project;
use App\Model\Entity\Vote;
use App\Model\Table\FundingCyclesTable;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\InternalErrorException;
use Cake\Http\Exception\MethodNotAllowedException;

/**
 * @property \App\Model\Table\VotesTable $Votes
 */
class VotesController extends ApiController
{
    /**
     * Allows unauthenticated votes, assigned to an arbitrary user
     * @var bool
     */
    private bool $testingMode = false;

    /**
     * beforeFilter callback method
     *
     * @param \Cake\Event\EventInterface $event Event object
     * @return \Cake\Http\Response|void|null
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        if ($this->testingMode) {
            $this->Authentication->allowUnauthenticated([
                'index',
            ]);
        }
    }

    /**
     * POST /api/votes endpoint
     *
     * @return void
     * @throws BadRequestException
     * @throws ForbiddenException
     * @throws InternalErrorException
     * @throws MethodNotAllowedException
     */
    public function index()
    {
        if (!$this->request->is(['post', 'options'])) {
            throw new MethodNotAllowedException('Only POST is supported at this endpoint. Method ' . $this->request->getMethod() . ' is not allowed.');
        }

        /* For some reason, submitting this data as application/json resulted in a blank request body.
         * The hack to get around this is to submit JSON as application/x-www-form-urlencoded, then decode it. */
        $data = json_decode(file_get_contents("php://input"), true);
        if (!($data['projects'] ?? false)) {
            throw new BadRequestException('No votes were submitted in your request.');
        }
        if (!isset($data['fundingCycleId'])) {
            throw new BadRequestException('Funding cycle ID was missing from data.');
        }
        /** @var FundingCyclesTable $fundingCyclesTable */
        $fundingCyclesTable = $this->fetchTable('FundingCycles');
        if (!$fundingCyclesTable->exists(['id' => $data['fundingCycleId']])) {
            throw new BadRequestException("Funding cycle #{$data['fundingCycleId']} was not found.");
        }

        if ($this->testingMode) {
            $userId = 1;
        } else {
            $user = $this->getAuthUser();
            $userId = $user?->id;
            if (!$userId) {
                throw new ForbiddenException('You must be logged in to vote');
            }
            if (!$user->is_verified) {
                throw new ForbiddenException('Your account must be verified before you vote');
            }
        }

        $fundingCycleId = $data['fundingCycleId'];
        $alreadyVoted = $this->Votes->hasVoted($userId, $fundingCycleId);
        if ($alreadyVoted && !Configure::read('allowRepeatVoting')) {
            throw new ForbiddenException('You have already voted in this funding cycle');
        }

        $projectCount = count($data['projects']);
        $projectsTable = $this->fetchTable('Projects');
        $newVotes = [];
        foreach ($data['projects'] as $i => $projectId) {
            /** @var \App\Model\Entity\Vote $vote */
            $vote = $this->Votes->newEmptyEntity();
            $vote->user_id = $userId;
            $vote->project_id = $projectId;
            $vote->funding_cycle_id = $fundingCycleId;
            $rank = $i + 1;
            $vote->weight = Vote::calculateWeight($rank, $projectCount);

            // Verify that project is a valid voting target
            $valid = $projectsTable->exists([
                'Projects.id' => $projectId,
                'Projects.funding_cycle_id' => $fundingCycleId,
                'Projects.status_id' => Project::STATUS_ACCEPTED,
            ]);
            if (!$valid) {
                throw new BadRequestException(
                    "Project #{$projectId} either does not exist or cannot currently be voted on."
                );
            }

            // Check all votes for errors before saving any votes, since any saved vote will block the user from
            // re-submitting votes in this funding cycle
            $error = $vote->getErrors();
            if ($error || !$this->Votes->rulesChecker()->check($vote, 'create')) {
                throw new InternalErrorException(
                    'There was an error submitting your votes. Details: ' . print_r($error, true)
                );
            }
            $newVotes[] = $vote;
        }
        foreach ($newVotes as $vote) {
            if (!$this->Votes->save($vote)) {
                $error = $vote->getErrors();
                throw new InternalErrorException(
                    'There was an error submitting your votes. Details: ' . print_r($error, true)
                );
            }
        }

        $this->set(['result' => true]);
        $this->viewBuilder()->setOption('serialize', ['result']);
        $this->viewBuilder()->setClassName('Json');
    }
}
