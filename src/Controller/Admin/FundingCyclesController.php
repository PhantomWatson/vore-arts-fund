<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;

/**
 * FundingCyclesController
 *
 * @property \App\Model\Table\FundingCyclesTable $FundingCycles
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */

class FundingCyclesController extends AppController
{
    /**
     * Funding cycles index page
     *
     * @return void
     */
    public function index()
    {
        $this->title('Funding Cycles');
        $this->set([
            'fundingCycles' => [
                'past' => $this->FundingCycles->find('past')->all(),
                'current' => $this->FundingCycles->find('current')->all(),
                'future' => $this->FundingCycles->find('future')->all(),
            ],
        ]);
    }

    /**
     * Page for adding a new funding cycle
     *
     * @return void
     */
    public function add()
    {
        $fundingCycle = $this->FundingCycles->newEmptyEntity();
        if ($this->request->is('post')) {
            $fundingCycle = $this->FundingCycles->patchEntity($fundingCycle, $this->request->getData());
            if ($this->FundingCycles->save($fundingCycle)) {
                $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Error creating funding cycle'));
            }
        }
        $this->title('Add Funding Cycle');
        $this->set(compact('fundingCycle'));
    }

    /**
     * Page for updating a funding cycle
     *
     * @return void
     */
    public function edit()
    {
        $fundingCycle = $this->FundingCycles
            ->find()
            ->where(['id' => $this->request->getParam('id')])
            ->first();
        if ($this->request->is('put')) {
            $updatedFundingCycle = $this->request->getData();
            $fundingCycle = $this->FundingCycles->get($updatedFundingCycle['id']);
            $fundingCycle = $this->FundingCycles->patchEntity($fundingCycle, $updatedFundingCycle);
            if ($this->FundingCycles->save($fundingCycle)) {
                $this->Flash->success(__('Successfully updated funding cycle'));
            } else {
                $this->Flash->error(__('Error updating funding cycle'));
            }
        }

        $this->set([
            'fundingCycle' => $fundingCycle,
            'title' => 'Edit Funding Cycle',
        ]);
    }
}
