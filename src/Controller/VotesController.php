<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\FundingCycle;
use App\Model\Entity\User;
use App\Model\Table\FundingCyclesTable;
use Cake\Event\EventInterface;
use Cake\Http\Response;

/**
 * @property \App\Model\Table\ApplicationsTable $Applications
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
    public function index($fundingCycleId = null)
    {
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
        } elseif ($cyclesCurrentlyVoting->count() > 1) {
            $this->set('title', 'Select funding cycle');
            return $this->render('chooseCycle');
        } else {
            $cycle = $cyclesCurrentlyVoting->first();
        }

        $applications = $cycle
            ? $this->fetchTable('Applications')
                ->find('forVoting', ['funding_cycle_id' => $cycle->id])
                ->all()
                ->toArray()
            : [];
        $user = $this->Authentication->getIdentity();
        $hasVoted = $user && $cycle && $this->Votes->hasVoted($user->id, $cycle->id);
        $nextCycle = $fundingCyclesTable->find('nextVoting')->first();
        $showUpcoming = $hasVoted || !$cycle || !$applications;
        $canVote = $user && $user->is_verified && !$showUpcoming;

        $this->title(
            $cycle
                ? 'Vote: ' . $cycle->name
                : (
                    $nextCycle
                        ? 'Voting begins ' . $nextCycle->vote_begin->format('F j, Y')
                        : 'Check back later for voting info'
                )
        );

        $toLoad = $this->getVoteAppFiles();

        $this->set(compact(
            'applications',
            'canVote',
            'cycle',
            'hasVoted',
            'nextCycle',
            'showUpcoming',
            'toLoad',
        ));
        $this->set([
            'isVerified' => $user ? $user->is_verified : false,
        ]);

        return null;
    }

    /**
     * Return the names of the JS and CSS files that need to be loaded
     *
     * @return array[]
     */
    private function getVoteAppFiles(): array
    {
        $retval = [
            'js' => [],
            'css' => [],
        ];
        $dist = WWW_ROOT . 'vote-app' . DS . 'dist';
        $files = scandir($dist);
        foreach ($files as $file) {
            if (preg_match('/\.bundle\.js$/', $file) === 1) {
                $retval['js'][] = $file;
            }
        }
        $files = scandir($dist . DS . 'styles');
        foreach ($files as $file) {
            if (preg_match('/\.css$/', $file) === 1) {
                $retval['css'][] = $file;
            }
        }

        return $retval;
    }
}
