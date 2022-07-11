<?php
declare(strict_types=1);

namespace App\Controller\API;

use App\Controller\AppController;
use App\Model\Table\FundingCyclesTable;
use Cake\Http\Exception\MethodNotAllowedException;

/**
 * ApplicationsController
 *
 * @property \App\Model\Table\ApplicationsTable $Applications
 */
class ApplicationsController extends AppController
{
    /**
     * GFET /api/applications endpoint
     *
     * @return void
     */
    public function index()
    {
        if (!$this->request->is('get')) {
            throw new MethodNotAllowedException('Only GET is supported at this endpoint');
        }
        /** @var FundingCyclesTable $fundingCyclesTable */
        $fundingCyclesTable = $this->fetchTable('FundingCycles');
        $fundingCycle = $fundingCyclesTable->find('currentVoting')->first();
        $applications = $fundingCycle
            ? $this->Applications->find('forVoting', ['funding_cycle_id' => $fundingCycle->id])->all()
            : [];
        $this->set(compact('applications'));
        $this->viewBuilder()->setOption('serialize', ['applications']);
        $this->viewBuilder()->setClassName('Json');
    }
}
