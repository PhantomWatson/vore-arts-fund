<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Application;
use App\Model\Entity\FundingCycle;
use Cake\Event\EventInterface;
use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;

/**
 * FundingCyclesController
 *
 * @property \App\Model\Table\FundingCyclesTable $FundingCycles
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */

class FundingCyclesController extends AdminController
{
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->addControllerBreadcrumb('Funding Cycles');
    }

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
     * @return \Cake\Http\Response|null
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
                $this->Flash->success('Funding cycle added');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('Error creating funding cycle');
            }
        } else {
            $start = new FrozenTime('12:00am', Application::LOCAL_TIMEZONE);
            $start = $start->day(1);
            $end = new FrozenTime('11:59pm', Application::LOCAL_TIMEZONE);
            $end = $end->lastOfMonth();
            $end = $end->setTime(23, 59, 59);
            $fundingCycle->application_begin = $start;
            $fundingCycle->application_end = $end;
            $fundingCycle->resubmit_deadline = $end;
            $fundingCycle->vote_begin = $start;
            $fundingCycle->vote_end = $end;
        }

        $fundingCycle = $fundingCycle->convertToLocalTimes();

        $this->title('Add Funding Cycle');
        $this->set(compact('fundingCycle'));
        $this->viewBuilder()->setTemplate('form');
        return null;
    }


    /**
     * Converts a local time into UTC for storage
     *
     * @param string $time Time string
     * @return \Cake\Chronos\ChronosInterface|\Cake\I18n\FrozenTime
     */
    public static function convertTimeToUtc($time): \Cake\Chronos\ChronosInterface|FrozenTime
    {
        return (new FrozenTime($time, Application::LOCAL_TIMEZONE))->setTimezone('UTC');
    }

    /**
     * Page for updating a funding cycle
     *
     * @return \Cake\Http\Response|null
     */
    public function edit()
    {
        $fundingCycleId = $this->request->getParam('id');
        $fundingCycle = $this->FundingCycles->get($fundingCycleId);
        if ($this->request->is('put')) {
            $data = $this->request->getData();
            foreach (FundingCycle::TIME_FIELDS as $field) {
                $data[$field] = $this->convertTimeToUtc($data[$field]);
            }

            $fundingCycle = $this->FundingCycles->patchEntity($fundingCycle, $data);
            if ($this->FundingCycles->save($fundingCycle)) {
                $this->Flash->success(__('Successfully updated funding cycle'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Error updating funding cycle'));
            }
        }

        $fundingCycle = $fundingCycle->convertToLocalTimes();

        $this->set([
            'fundingCycle' => $fundingCycle,
            'title' => 'Edit Funding Cycle',
        ]);
        $this->viewBuilder()->setTemplate('form');
        return null;
    }

    public function projects($fundingCycleId)
    {
        $fundingCycle = $this->FundingCycles->get($fundingCycleId);
        $projectsTable = TableRegistry::getTableLocator()->get('Projects');
        $projects = $projectsTable
            ->find()
            ->where(['Projects.funding_cycle_id' => $fundingCycleId])
            ->orderDesc('Projects.created')
            ->all();

        $this->title('Projects');
        $this->addBreadcrumb(
            $fundingCycle->name,
            [
                'prefix' => 'Admin',
                'controller' => 'FundingCycles',
                'action' => 'edit',
                'id' => $fundingCycleId,
            ],
        );
        $this->addBreadcrumb('Projects', []);

        $this->set([
            'projects' => $projects
        ]);
    }
}
