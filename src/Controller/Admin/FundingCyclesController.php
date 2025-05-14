<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Application;
use App\Model\Entity\FundingCycle;
use Cake\Event\EventInterface;
use Cake\I18n\FrozenTime;
use Cake\ORM\Query;
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
        function getCycles(Query $query) {
            return $query->orderAsc('application_begin')
                ->contain([
                    'Projects' => function (Query $q)
                    {
                        return $q
                            ->select([
                                'Projects.funding_cycle_id',
                                'count' => $q->func()->count('Projects.id')
                            ])
                            ->group('Projects.funding_cycle_id');
                    }
                ])
                ->all();
        }
        $this->set([
            'fundingCycles' => [
                'past' => getCycles($table->find('past')->find('orderedDesc')),
                'current' => getCycles($table->find('current')->find('orderedDesc')),
                'future' => getCycles($table->find('future')->find('orderedDesc')),
            ],
        ]);
    }

    private function convertFormDataToUtc($data)
    {
        foreach (FundingCycle::TIME_START_FIELDS as $field) {
            $data[$field] = (new FrozenTime($data[$field], Application::LOCAL_TIMEZONE))
                ->setTime(0, 0)
                ->setTimezone('UTC');
        }
        foreach (FundingCycle::TIME_END_FIELDS as $field) {
            $data[$field] = (new FrozenTime($data[$field], Application::LOCAL_TIMEZONE))
                ->setTime(23, 59, 59)
                ->setTimezone('UTC');
        }
        return $data;
    }

    /**
     * Page for adding a new funding cycle
     *
     * @return \Cake\Http\Response|null
     */
    public function add()
    {
        /** @var \App\Model\Entity\FundingCycle $fundingCycle */
        if ($this->request->is('post')) {

            // Adjust times and timezones before validation
            $data = $this->request->getData();
            $data = $this->convertFormDataToUtc($data);
            $fundingCycle = $this->FundingCycles->newEntity($data);

            $fundingCycleToSave = $this->FundingCycles->patchEntity($fundingCycle, $data);
            if ($this->FundingCycles->save($fundingCycleToSave)) {
                $this->Flash->success('Funding cycle added');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('Error creating funding cycle');
            }
        } else {
            $fundingCycle = $this->FundingCycles->newEmptyEntity();
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

        if ($this->request->is(['put', 'patch'])) {
            // Adjust times and timezones before validation
            $data = $this->request->getData();
            $data = $this->convertFormDataToUtc($data);
            $fundingCycle = $this->FundingCycles->patchEntity($fundingCycle, $data);

            if ($this->FundingCycles->save($fundingCycle)) {
                $this->Flash->success(__('Successfully updated funding cycle'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('Error updating funding cycle');
            }
        }

        $this->set([
            'fundingCycle' => $fundingCycle,
            'title' => 'Edit Funding Cycle',
        ]);
        $this->viewBuilder()->setTemplate('form');
        return null;
    }

    public function projects()
    {
        $fundingCycleId = $this->request->getParam('id');
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
