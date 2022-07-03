<?php
declare(strict_types=1);

namespace App\Controller;

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
        $this->loadModel('Applications');
        $this->loadModel('FundingCycles');
        $this->loadModel('Votes');
    }

    /**
     * Votes index page
     *
     * @return void
     */
    public function index()
    {
        $applications = $this->Applications
            ->find()
            ->where(['status_id' => 5 ])
            ->all()
            ->toArray();
        $title = 'Applications';

        $this->set(compact(
            'applications',
            'title',
        ));
    }

    /**
     * @return \Cake\Http\Response|null
     */
    public function submit(): ?Response
    {
        $this->set([
            'applications' => $this->Applications
                ->find()
                ->where(['status_id' => 5])
                ->all()
                ->toArray(),
            'title' => 'Vote',
        ]);

        $now = date('Y-m-d H:i:s');
        $fundingCycle = $this->FundingCycles
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
        foreach ($keys as $key) {
            /** @var \App\Model\Entity\Vote $voteEntry */
            $voteEntry = $this->Votes->newEmptyEntity();
            $user = $this->request->getAttribute('identity');
            $voteEntry->user_id = $user ? $user->id : null;
            $voteEntry->application_id = $key;
            $voteEntry->funding_cycle_id = $fundingCycle->id;
            $voteEntry->weight = 1;
            if (!$this->Votes->save($voteEntry)) {
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
