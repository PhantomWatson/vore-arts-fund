<?php
declare(strict_types=1);

namespace App\Controller\My;

use App\Controller\ApplicationsController as BaseApplicationsController;
use App\Model\Entity\Application;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\ORM\TableRegistry;

/**
 * ApplicationsController
 *
 * @property \App\Model\Table\ApplicationsTable $Applications
 * @property \App\Model\Table\CategoriesTable $Categories
 * @property \App\Model\Table\FundingCyclesTable $FundingCycles
 * @property \App\Model\Table\ImagesTable $Images
 */
class ApplicationsController extends BaseApplicationsController
{
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->Applications = $this->fetchTable('Applications');

        $this->addControllerBreadcrumb('My Applications');
    }

    /**
     * Page for viewing one's own application
     *
     * @return \Cake\Http\Response|null
     */
    public function viewMy(): ?Response
    {
        $applicationId = $this->request->getParam('id');
        if (!$this->isOwnApplication($applicationId)) {
            $this->Flash->error('Sorry, but that application is not available to view');
            return $this->redirect('/');
        }

        return $this->_view();
    }

    /**
     * Page for withdrawing an application from consideration
     *
     * @return void
     */
    public function withdraw()
    {
        $id = $this->request->getParam('id');
        $application = $this->Applications->find()->where(['id' => $id])->first();
        if ($this->request->is('post')) {
            $application = $this->Applications->patchEntity($application, ['status_id' => Application::STATUS_WITHDRAWN]);
            if ($this->Applications->save($application)) {
                $this->Flash->success('Application withdrawn.');
            }
        }
        $this->set(['title' => 'Withdraw']);
    }

    /**
     * Page for updating a draft or (re)submitting an application
     *
     * @return \Cake\Http\Response|null
     */
    public function edit(): ?Response
    {
        // Confirm application exists
        $applicationId = $this->request->getParam('id');
        if (!$this->isOwnApplication($applicationId)) {
            $this->Flash->error('That application was not found');
            return $this->redirect('/');
        }

        // Confirm application can be updated
        /** @var Application $application */
        $application = $this->Applications->getForForm($applicationId);
        if (!$application->isUpdatable()) {
            $this->Flash->error('That application cannot currently be updated.');
            return $this->redirect('/');
        }

        // Set up view vars
        $this->title('Update and Submit');
        $this->viewBuilder()->setTemplate('/Applications/form');
        $this->setFromNow($application->getSubmitDeadline());
        $this->setApplicationVars();

        // Process form
        if ($this->request->is('put')) {
            $data = $this->request->getData();

            // If saving, status doesn't change. Otherwise, it's submitted for review.
            $savingToDraft = isset($data['save']);
            if (!$savingToDraft) {
                $application->status_id = Application::STATUS_UNDER_REVIEW;
            }

            if ($this->processApplication($application, $data)) {
                return $this->redirect(['action' => 'index']);
            }
        } else {
            $identity = $this->Authentication->getIdentity();
            $userId = $identity->getIdentifier();
            $usersTable = TableRegistry::getTableLocator()->get('Users');
            $user = $usersTable->get($userId);
            $application->address = $user->address;
            $application->zipcode = $user->zipcode;
        }

        $this->set(compact('application'));

        return null;
    }

    /**
     * Page for removing an application
     *
     * @return void
     */
    public function delete()
    {
        $id = $this->request->getParam('id');
        $application = $this->Applications->get($id);
        if ($this->request->is(['delete', 'post']) && $this->Applications->delete($application)) {
            $this->Flash->success('Application has been deleted');
        } else {
            $this->Flash->error('There was an error deleting that application');
        }
        return $this->redirect($this->referer());
    }

    public function index()
    {
        $this->title('My Applications');
        /** @var \App\Model\Entity\User $user */
        $user = $this->Authentication->getIdentity();
        $applications = $this->Applications
            ->find()
            ->where(['user_id' => $user->id])
            ->orderDesc('Applications.created')
            ->contain(['FundingCycles', 'Reports'])
            ->all();
        $this->set(compact('applications'));
    }
}
