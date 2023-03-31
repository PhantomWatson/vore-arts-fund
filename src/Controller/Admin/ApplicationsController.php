<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Event\MailListener;
use App\Model\Entity\Application;
use App\Model\Entity\User;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\Http\Response;

/**
 * FundingCyclesController
 *
 * @property \App\Model\Table\ApplicationsTable $Applications
 * @property \App\Model\Table\CategoriesTable $Categories
 * @property \App\Model\Table\ImagesTable $Images
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */

class ApplicationsController extends AdminController
{
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->addControllerBreadcrumb('Applications');
    }

    /**
     * Applications index page
     *
     * @return void
     */
    public function index($fundingCycleId = null)
    {
        $this->title('Applications');
        $fundingCyclesTable = $this->fetchTable('FundingCycles');
        $applications = [];

        if (!$fundingCycleId) {
            $currentCycle = $fundingCyclesTable->find('current')->first();
            $fundingCycleId = $currentCycle ? $currentCycle->id : null;
        }

        if ($fundingCycleId) {
            $applications = $this
                ->Applications
                ->find()
                ->where(['funding_cycle_id' => $fundingCycleId])
                ->all();
        }

        $this->set([
            'applications' => $applications,
            'fundingCycles' => $fundingCyclesTable->find()->orderDesc('application_begin')->all(),
            'fundingCycleId' => $fundingCycleId,
        ]);
    }

    /**
     * Page for reviewing an application
     *
     * @return Response|null
     */
    public function review()
    {
        // Activate event listener
        $mailListener = new MailListener();
        $this->getEventManager()->on($mailListener);

        $applicationId = $this->request->getParam('id');
        $application = $this->Applications->getForViewing($applicationId);
        if (!$application) {
            $this->Flash->error('Application not found');
            return $this->redirect(['action' => 'index']);
        }

        $notesTable = $this->fetchTable('Notes');
        if (!$this->request->is('get')) {
            $data = $this->request->getData();

            // Updating status
            if ($data['status_id'] ?? false) {
                $application->status_id = (int)$data['status_id'];
                if ($this->Applications->save($application)) {
                    $this->Flash->success('Status updated');
                    $this->dispatchStatusChangeEvent($application);
                } else {
                    $this->Flash->error('Error updating status');
                }
            }

            // Adding note
            if ($data['body'] ?? false) {
                /** @var User $user */
                $user = $this->Authentication->getIdentity();
                $data['user_id'] = $user->id;
                $data['application_id'] = $applicationId;
                $note = $notesTable->newEntity($data);
                if ($notesTable->save($note)) {
                    $this->Flash->success('Note added');
                } else {
                    $this->Flash->error('Error adding note');
                }
            }
        }

        $statuses = Application::getStatuses();
        $validStatuses = Application::getValidStatusOptions($application->status_id);
        $statusOptions = [];
        foreach ($validStatuses as $statusId) {
            $statusOptions[$statusId] = $statuses[$statusId];
        }

        // Set view vars
        $questionsTable = $this->fetchTable('Questions');
        $questions = $questionsTable->find('forApplication')->toArray();
        $notes = $notesTable
            ->find()
            ->where(['application_id' => $applicationId])
            ->contain(['Users'])
            ->orderDesc('Notes.created')
            ->all();
        $newNote = $notesTable->newEmptyEntity();
        $this->set(compact(
            'application',
            'newNote',
            'notes',
            'questions',
            'statusOptions',
        ));
        $this->title('Application: ' . $application->title);
        $this->setCurrentBreadcrumb($application->title);

        return null;
    }

    /**
     * @param Application $application
     * @return void
     */
    private function dispatchStatusChangeEvent(Application $application)
    {
        switch ($application->status_id) {
            case Application::STATUS_ACCEPTED:
                $event = new Event('Application.accepted', $this, compact('application'));
                break;
            case Application::STATUS_REVISION_REQUESTED:
                $event = new Event('Application.revisionRequested', $this, compact('application'));
                break;
            case Application::STATUS_REJECTED:
                $event = new Event('Application.rejected', $this, compact('application'));
                break;
            default;
                return;
        }
        $this->getEventManager()->dispatch($event);
    }
}
