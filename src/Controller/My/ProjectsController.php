<?php
declare(strict_types=1);

namespace App\Controller\My;

use App\Controller\ProjectsController as BaseProjectsController;
use App\Model\Entity\Project;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\ORM\TableRegistry;

/**
 * ProjectsController
 *
 * @property \App\Model\Table\ProjectsTable $Projects
 * @property \App\Model\Table\CategoriesTable $Categories
 * @property \App\Model\Table\FundingCyclesTable $FundingCycles
 * @property \App\Model\Table\ImagesTable $Images
 */
class ProjectsController extends BaseProjectsController
{
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->Projects = $this->fetchTable('Projects');

        $this->addControllerBreadcrumb('My Projects');
    }

    /**
     * Page for viewing one's own project
     *
     * @return \Cake\Http\Response|null
     */
    public function view(): ?Response
    {
        $projectId = $this->request->getParam('id');
        if (!$this->isOwnProject($projectId)) {
            $this->Flash->error('Sorry, but that project is not available to view.');
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
        $project = $this->Projects->find()->where(['id' => $id])->first();
        if ($this->request->is('post')) {
            $project = $this->Projects->patchEntity($project, ['status_id' => Project::STATUS_WITHDRAWN]);
            if ($this->Projects->save($project)) {
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
        // Confirm project exists
        $projectId = $this->request->getParam('id');
        if (!$this->isOwnProject($projectId)) {
            $this->Flash->error('That project was not found.');
            return $this->redirect('/');
        }

        // Confirm project can be updated
        /** @var Project $project */
        $project = $this->Projects->getForForm($projectId);
        if (!$project->isUpdatable()) {
            $this->Flash->error(
                'That application cannot currently be updated. ' . $this->errorContactMsg,
                ['escape' => false]
            );
            return $this->redirect('/');
        }

        // Set up view vars
        $this->title('Update and Submit');
        $this->viewBuilder()->setTemplate('/Projects/form');
        $this->setFromNow($project->getSubmitDeadline());
        $this->setProjectVars();

        // Process form
        if ($this->request->is('put')) {
            $data = $this->request->getData();

            // If saving, status doesn't change. Otherwise, it's submitted for review.
            $savingToDraft = isset($data['save']);
            if (!$savingToDraft) {
                $project->status_id = Project::STATUS_UNDER_REVIEW;
            }

            if ($this->processProject($project, $data)) {
                return $this->redirect(['action' => 'index']);
            }
        } else {
            $user = $this->getAuthUser();
            $project->address = $user->address;
            $project->zipcode = $user->zipcode;
        }

        $this->set(compact('project'));

        return null;
    }

    /**
     * Page for removing an application
     *
     * @return Response
     */
    public function delete()
    {
        $id = $this->request->getParam('id');
        $project = $this->Projects->get($id);
        if ($this->request->is(['delete', 'post']) && $this->Projects->delete($project)) {
            $this->Flash->success('Application has been deleted');
        } else {
            $this->Flash->error(
                'There was an error deleting that application. ' . $this->errorTryAgainContactMsg,
                ['escape' => false]
            );
        }
        return $this->redirect($this->referer());
    }

    public function index()
    {
        $this->title('My Projects');
        $user = $this->getAuthUser();
        $projects = $this->Projects
            ->find()
            ->where(['user_id' => $user->id])
            ->orderDesc('Projects.created')
            ->contain(['FundingCycles', 'Reports'])
            ->all();
        $this->set(compact('projects'));
    }
}
