<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\FundingCycle;
use App\Model\Entity\User;
use App\Model\Table\FundingCyclesTable;
use Cake\Event\EventInterface;
use Cake\Http\Response;

/**
 * @property \App\Model\Table\ProjectsTable $Projects
 * @property \App\Model\Table\VotesTable $Votes
 * @property \App\Model\Table\FundingCyclesTable $FundingCycles
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 */
class VotesController extends AppController
{
    /**
     * @param EventInterface $event
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['index', 'view']);
    }

    /**
     * Votes index page
     *
     * @return Response|null
     */
    public function index()
    {
        $fundingCycleId = $this->request->getParam('id');
        // Get specified cycle, or the only current cycle, or prompt user to select a cycle
        /**
         * @var FundingCyclesTable $fundingCyclesTable
         * @var FundingCycle|null $cycle
         * @var FundingCycle|null $nextCycle
         * @var User|null $user
         */
        $fundingCyclesTable = $this->fetchTable('FundingCycles');
        $cyclesCurrentlyVoting = $fundingCyclesTable->find('currentVoting');
        $this->set(compact('cyclesCurrentlyVoting'));
        if ($fundingCycleId) {
            try {
                $cycle = $fundingCyclesTable->get($fundingCycleId);
            } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
                $this->Flash->error("Sorry, we couldn't find funding cycle #$fundingCycleId.");
                return $this->redirect(['action' => 'index']);
            }
        } elseif ($cyclesCurrentlyVoting->all()->count() > 1) {
            $this->set('title', 'Select funding cycle');
            return $this->render('chooseCycle');
        } else {
            $cycle = $cyclesCurrentlyVoting->first();
        }

        $projects = $cycle
            ? $this->fetchTable('Projects')
                ->find('forVoting', ['funding_cycle_id' => $cycle->id])
                ->all()
                ->toArray()
            : [];
        $user = $this->getAuthUser();
        $isVerified = $user ? $user->is_verified : false;
        $hasVoted = $user && $cycle && $this->Votes->hasVoted($user->id, $cycle->id);
        $nextCycle = $fundingCyclesTable->find('nextVoting')->first();
        $showUpcoming = $hasVoted || !$cycle || !$projects;
        $canVote = $user && $user->is_verified && !$showUpcoming && $projects;

        $this->setCurrentBreadcrumb('Vote');
        $title = $projects
            ? 'Vote on Funding Applications'
            : ($nextCycle
                ? 'Voting begins ' . $nextCycle->vote_begin->format('F j, Y')
                : 'Check back later for voting info'
            );
        $this->title($title);
        $toLoad = $this->getAppFiles('vote-app');
        $this->set(compact(
            'canVote',
            'cycle',
            'hasVoted',
            'isVerified',
            'nextCycle',
            'projects',
            'showUpcoming',
            'toLoad',
        ));
        $this->viewBuilder()->setLayout('vote');

        if (!$user) {
            $template = 'not_logged_in';
        } elseif (!$isVerified) {
            $template = 'not_verified';
        } elseif ($hasVoted) {
            $template = 'already_voted';
        } elseif (!$projects) {
            $template = 'no_projects';
        } else {
            $template = 'index';
        }
        $this->viewBuilder()->setTemplate($template);

        return null;
    }
}
