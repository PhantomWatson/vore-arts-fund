<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Application;
use App\Controller\AppController;
use App\Model\Entity\FundingCycle;
use Cake\I18n\FrozenTime;

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
        $table = $this->FundingCycles;
        $this->set([
            'fundingCycles' => [
                'past' => $table->find('past')->orderAsc('application_begin')->all(),
                'current' => $table->find('current')->orderAsc('application_begin')->all(),
                'future' => $table->find('future')->orderAsc('application_begin')->all(),
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
        /** @var \App\Model\Entity\FundingCycle $fundingCycle */
        $fundingCycle = $this->FundingCycles->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            foreach (FundingCycle::TIME_FIELDS as $field) {
                $data[$field] = $this->convertTimeToUtc($data[$field]);
            }
            $fundingCycle = $this->FundingCycles->patchEntity($fundingCycle, $data);
            if ($this->FundingCycles->save($fundingCycle)) {
                $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Error creating funding cycle'));
            }
        } else {
            $start = new FrozenTime('12:00am', Application::LOCAL_TIMEZONE);
            $start = $start->day(1);
            $end = new FrozenTime('11:59pm', Application::LOCAL_TIMEZONE);
            $end = $end->lastOfMonth();
            $end = $end->setTime(23, 59, 59);
            $fundingCycle->application_begin = $start;
            $fundingCycle->application_end = $end;
            $fundingCycle->vote_begin = $start;
            $fundingCycle->vote_end = $end;
        }
        $this->title('Add Funding Cycle');
        $this->set(compact('fundingCycle'));
    }


    /**
     * Converts a local time into UTC for storage
     *
     * @param string $time Time string
     * @return \Cake\Chronos\ChronosInterface|\Cake\I18n\FrozenTime
     */
    private function convertTimeToUtc($time)
    {
        return (new FrozenTime($time, \App\Application::LOCAL_TIMEZONE))->setTimezone('UTC');
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
            $data = $this->request->getData();
            foreach (FundingCycle::TIME_FIELDS as $field) {
                $data[$field] = $this->convertTimeToUtc($data[$field]);
            }
            $fundingCycle = $this->FundingCycles->get($data['id']);
            $fundingCycle = $this->FundingCycles->patchEntity($fundingCycle, $data);
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
