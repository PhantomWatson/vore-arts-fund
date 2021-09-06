<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

/**
 * FundingCycles Controller
 *
 * @property \App\Model\Table\FundingCyclesTable $FundingCycles
 * @method \App\Model\Entity\FundingCycle[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class FundingCyclesController extends AppController
{
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->Auth->allow();
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
            ->orderAsc('application_end');

        $this->set([
            'fundingCycles' => $fundingCycles,
            'title' => 'Funding Cycles'
        ]);
    }
}
