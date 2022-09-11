<?php
declare(strict_types=1);

namespace App\Controller\API;

use App\Model\Entity\Application;
use App\Model\Entity\FundingCycle;
use App\Model\Entity\User;
use App\Model\Entity\Vote;
use App\Model\Table\FundingCyclesTable;
use Cake\Event\EventInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\InternalErrorException;
use Cake\Http\Exception\MethodNotAllowedException;

/**
 * ApplicationsController
 *
 * @property \App\Model\Table\VotesTable $Votes
 */
class VotesController extends ApiController
{
    /**
     * Allows unauthenticated votes, assigned to an arbitrary user
     * @var bool
     */
    private bool $testingMode = true;

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
        if (!($data['applications'] ?? false)) {
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
            /** @var User $user */
            $user = $this->request->getAttribute('identity');
            $userId = $user->id ?? false;
            if ($userId === false) {
                throw new ForbiddenException('You must be logged in to vote');
            }
            if (!$user->is_verified) {
                throw new ForbiddenException('Your account must be verified before you vote');
            }
        }

        /** @var FundingCycle $fundingCycle */
        $fundingCycleId = $data['fundingCycleId'];
        $applicationCount = count($data['applications']);
        $applicationsTable = $this->fetchTable('Applications');
        foreach ($data['applications'] as $i => $application) {
            /** @var \App\Model\Entity\Vote $vote */
            $vote = $this->Votes->newEmptyEntity();
            $vote->user_id = $userId;
            $vote->application_id = $application['id'];
            $vote->funding_cycle_id = $fundingCycleId;
            $rank = $i + 1;
            $vote->weight = Vote::calculateWeight($rank, $applicationCount);

            // Verify that application is a valid voting target
            $valid = $applicationsTable->exists([
                'Applications.id' => $application['id'],
                'Applications.funding_cycle_id' => $fundingCycleId,
                'Applications.status_id' => Application::STATUS_ACCEPTED,
            ]);
            if (!$valid) {
                throw new BadRequestException(
                    "Application #{$application['id']} either does not exist or cannot currently be voted on."
                );
            }

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
