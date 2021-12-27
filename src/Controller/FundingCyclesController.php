<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

/**
 * FundingCycles Controller
 *
 * @property \App\Model\Table\FundingCyclesTable $FundingCycles
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 * @method \App\Model\Entity\FundingCycle[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class FundingCyclesController extends AppController
{
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['index']);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $fundingCycles = $this->FundingCycles
            ->find('currentAndFuture')
            ->orderAsc('application_end')
            ->all();

        $this->set([
            'fundingCycles' => $fundingCycles,
            'title' => 'Funding Cycles'
        ]);
    }
}
