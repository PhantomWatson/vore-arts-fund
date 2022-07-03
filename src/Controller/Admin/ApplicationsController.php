<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Model\Entity\Application;
use App\Model\Entity\User;
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
        $applicationId = $this->request->getParam('id');
        $application = $this->Applications->get(
            $applicationId,
            [
                'contain' => [
                    'Answers',
                    'Categories',
                    'FundingCycles',
                    'Images',
                ]
            ]
        );
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

        $this->setViewApplicationViewVars($applicationId);
        $notes = $notesTable
            ->find()
            ->where(['application_id' => $applicationId])
            ->contain(['Users'])
            ->orderDesc('Notes.created')
            ->all();
        $newNote = $notesTable->newEmptyEntity();
        $this->set(compact('application', 'notes', 'newNote', 'statusOptions'));

        return null;
    }

    /**
     * Page for changing the status of an application
     *
     * @return void
     */
    public function setStatus()
    {
        $id = $this->request->getParam('id');

        if ($this->request->is('post')) {
            $application = $this->Applications->get($id);
            $application = $this->Applications->patchEntity($application, $this->request->getData());
            if ($this->Applications->save($application)) {
                $this->Flash->success(__('Successfully updated application status'));
            } else {
                $this->Flash->error(__('Error updating application status'));
            }
        }
    }
}
