<?php
declare(strict_types=1);

namespace App\Controller\API;

use App\Model\Table\FundingCyclesTable;
use Cake\Event\EventInterface;
use Cake\Http\Exception\MethodNotAllowedException;

/**
 * ProjectsController
 *
 * @property \App\Model\Table\ProjectsTable $Projects
 */
class ProjectsController extends ApiController
{
    /**
     * beforeFilter callback method
     *
     * @param \Cake\Event\EventInterface $event Event object
     * @return \Cake\Http\Response|void|null
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated([
            'index',
        ]);
    }

    /**
     * GET /api/projects endpoint
     *
     * @return void
     */
    public function index()
    {
        if (!$this->request->is(['get', 'options'])) {
            throw new MethodNotAllowedException('Only GET is supported at this endpoint');
        }
        /** @var FundingCyclesTable $fundingCyclesTable */
        $fundingCyclesTable = $this->fetchTable('FundingCycles');
        $fundingCycle = $fundingCyclesTable->find('currentVoting')->first();
        $projects = $fundingCycle
            ? $this->Projects->find('forVoting', ['funding_cycle_id' => $fundingCycle->id])->limit(3)->all()
            : [];
        $this->set(compact('projects'));
        $this->viewBuilder()->setOption('serialize', ['projects']);
        $this->viewBuilder()->setClassName('Json');
    }
}
