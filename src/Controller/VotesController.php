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
     * @return void
     */
    public function index()
    {
        // Get current cycle and applications
        /**
         * @var FundingCyclesTable $fundingCyclesTable
         * @var FundingCycle|null $cycle
         * @var FundingCycle|null $nextCycle
         * @var User|null $user
         */
        $fundingCyclesTable = $this->fetchTable('FundingCycles');
        $cycle = $fundingCyclesTable->find('currentVoting')->first();
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
            'isVerified' => $user->is_verified,
        ]);
    }

    /**
     * @return \Cake\Http\Response|null
     */
    public function submit(): ?Response
    {
        $this->set([
            'applications' => $this->fetchTable('Applications')
                ->find()
                ->where(['status_id' => 5])
                ->all()
                ->toArray(),
            'title' => 'Vote',
        ]);

        $now = date('Y-m-d H:i:s');
        $fundingCycle = $this->fetchTable('FundingCycles')
            ->find()
            ->where([
                'FundingCycles.application_begin <=' => $now,
                'FundingCycles.application_end >=' => $now,
            ])
            ->select(['FundingCycles.id'])
            ->first();

        if (!$this->request->is('post')) {
            return null;
        }

        $data = $this->request->getData();
        $keys = array_keys($data);

        $success = false;
        $votesTable = $this->fetchTable('Votes');
        foreach ($keys as $key) {
            /** @var \App\Model\Entity\Vote $voteEntry */
            $voteEntry = $votesTable->newEmptyEntity();
            $user = $this->request->getAttribute('identity');
            $voteEntry->user_id = $user ? $user->id : null;
            $voteEntry->application_id = $key;
            $voteEntry->funding_cycle_id = $fundingCycle->id;
            $voteEntry->weight = 1;
            if (!$votesTable->save($voteEntry)) {
                break;
            }
            $success = true;
        }
        if ($success) {
            $this->Flash->success(__('Your votes have successfully been submitted.'));

            return $this->redirect('/');
        }

        $this->Flash->error(__('Your votes could not be submitted.'));

        return $this->redirect(['action' => 'index']);
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
