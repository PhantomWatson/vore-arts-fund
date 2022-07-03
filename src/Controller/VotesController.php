<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\FundingCycle;
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
        /** @var FundingCyclesTable $fundingCyclesTable */
        $fundingCyclesTable = $this->fetchTable('FundingCycles');
        /** @var FundingCycle|null $cycle */
        $cycle = $fundingCyclesTable->find('currentVoting')->first();
        /** @var FundingCycle|null $nextCycle */
        $nextCycle = $fundingCyclesTable->find('nextVoting')->first();
        $applications = $cycle
            ? $this->fetchTable('Applications')
                ->find('forVoting', ['funding_cycle_id' => $cycle->id])
                ->all()
            : [];
        $this->title(
            $cycle
                ? 'Vote: ' . $cycle->name
                : (
                    $nextCycle
                        ? 'Voting begins ' . $nextCycle->vote_begin->format('F j, Y')
                        : 'Check back later for voting info'
                )
        );
        $this->set(compact(
            'applications',
            'cycle',
            'nextCycle',
        ));
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
}
