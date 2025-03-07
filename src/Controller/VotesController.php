<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\FundingCycle;
use App\Model\Entity\User;
use App\Model\Table\FundingCyclesTable;
use Cake\Core\Configure;
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
        if ($fundingCycleId) {
            try {
                $cycle = $fundingCyclesTable->get($fundingCycleId);
            } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
                $this->Flash->error("Sorry, we couldn't find funding cycle #$fundingCycleId.");
                return $this->redirect(['action' => 'index']);
            }
        } else {
            $cyclesCurrentlyVoting = $fundingCyclesTable->find('currentVoting');
            $hasMultiplePossibleCycles = $cyclesCurrentlyVoting->all()->count() > 1;
            if ($hasMultiplePossibleCycles) {
                $this->set('title', 'Select funding cycle');
                $this->set(compact('cyclesCurrentlyVoting'));
                return $this->render('chooseCycle');
            }
            $cycle = $cyclesCurrentlyVoting->first();
        }

        $fundingCycleHasProjects = $cycle
            ? (bool)$this->fetchTable('Projects')
                ->find('forVoting', ['funding_cycle_id' => $cycle->id])
                ->select(['existing' => 1])
                ->limit(1)
                ->disableHydration()
                ->toArray()
            : [];
        $user = $this->getAuthUser();
        $isVerified = $user ? $user->is_verified : false;
        $hasVoted = $user && $cycle && $this->Votes->hasVoted($user->id, $cycle->id);
        $repeatVotesAllows = Configure::read('allowRepeatVoting');
        $blockedFromRepeatVoting = $hasVoted && !$repeatVotesAllows;
        $nextCycle = $fundingCyclesTable->find('nextVoting')->first();
        $showUpcoming = $blockedFromRepeatVoting || !$cycle || !$fundingCycleHasProjects;
        $canVote = $user && $user->is_verified && !$showUpcoming && $fundingCycleHasProjects;

        $this->setCurrentBreadcrumb('Vote');
        $title = $fundingCycleHasProjects
            ? 'Vote on Funding Applications'
            : ($nextCycle
                ? 'Voting begins ' . $nextCycle->vote_begin_local->format('F j, Y')
                : 'Check back later for voting info'
            );
        $this->title($title);
        $toLoad = $this->getAppFiles('vote-app/dist', 'vote-app/dist/styles');

        $this->set(compact(
            'canVote',
            'cycle',
            'hasVoted',
            'isVerified',
            'nextCycle',
            'showUpcoming',
            'toLoad',
            'fundingCycleHasProjects',
        ));
        $this->viewBuilder()->setLayout('vote');

        if (!$user) {
            $template = 'not_logged_in';
        } elseif (!$isVerified) {
            $template = 'not_verified';
        } elseif ($blockedFromRepeatVoting) {
            $template = 'already_voted';
        } elseif (!$fundingCycleHasProjects) {
            $template = 'no_projects';
        } else {
            $template = 'index';
            if ($hasVoted && $repeatVotesAllows) {
                $this->Flash->set(
                    'You\'ve already voted for this funding cycle, but the '
                    . getEnvironment() . ' environment allows repeat voting.'
                );
            }
        }
        $this->viewBuilder()->setTemplate($template);

        return null;
    }
}
