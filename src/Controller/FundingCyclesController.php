<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\ORM\Query;

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
        $this->Authentication->allowUnauthenticated([
            'index',
            'view',
        ]);
        $this->addControllerBreadcrumb('Funding Cycles');
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
            ->contain([
                'Projects' => function (Query $q) {
                    return $q->select([
                        'Projects.funding_cycle_id',
                        'Projects.status_id'
                    ]);
                }
            ])
            ->orderAsc('application_end')
            ->all();

        $this->set([
            'fundingCycles' => $fundingCycles,
            'title' => 'Funding Cycles',
        ]);
    }

    /**
     * Shows a single Funding Cycle
     *
     * @return void
     */
    public function view(): void
    {
        $id = $this->request->getParam('id');
        $fundingCycle = $this->FundingCycles->get($id);

        $this->set([
            'fundingCycle' => $fundingCycle,
            'title' => $fundingCycle->name . ' Funding Cycle',
        ]);
        $this->setCurrentBreadcrumb($fundingCycle->name);
    }
}
