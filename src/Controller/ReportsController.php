<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Project;
use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

/**
 * Reports Controller
 *
 * @property \App\Model\Table\ReportsTable $Reports
 * @method \App\Model\Entity\Report[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ReportsController extends AppController
{
    public $paginate = [
        'contain' => ['Users', 'Projects'],
        'limit' => 10,
        'order' => ['Reports.created' => 'desc'],
    ];

    /**
     * @param EventInterface $event
     * @return void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $reports = $this->paginate($this->Reports);

        $this->set(compact('reports'));
        $this->addControllerBreadcrumb();
    }

    /**
     * View method
     *
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view()
    {
        $id = $this->request->getParam('id');
        $report = $this->Reports->get($id, [
            'contain' => ['Users', 'Projects'],
        ]);
        $this->addBreadcrumbForProject($report->project);
        $this->addBreadcrumb(
            'Reports',
            [
                'prefix' => false,
                'controller' => 'Reports',
                'action' => 'project',
                'id' => $report->project->id,
            ]
        );
        $this->setCurrentBreadcrumb($report->created->format('F j, Y'));

        $this->set(compact('report'));
    }

    /**
     * @param Project $project
     * @param string|null $prefix Set to 'My' if in the 'my projects' context
     * @return void
     */
    private function addBreadcrumbForProject(Project $project, $prefix = null): void
    {
        $this->addBreadcrumb(
            $prefix . ' Projects',
            [
                'prefix' => $prefix,
                'controller' => 'Projects',
                'action' => 'index',
            ]
        );
        $this->addBreadcrumb(
            $project->title,
            [
                'prefix' => $prefix,
                'controller' => 'Projects',
                'action' => 'view',
                'id' => $project->id,
            ]
        );
    }

    /**
     * Submit method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function submit()
    {
        $projectId = $this->request->getParam('id') ?? $this->request->getData('project_id');
        $user = $this->getAuthUser();

        // If a project needs to be selected
        if (!$projectId) {
            $projects = $this
                ->Reports
                ->Projects
                ->find('reportableForUser', ['userId' => $user->id])
                ->select(['id', 'title'])
                ->orderAsc('title')
                ->all();
            if (count($projects) == 1) {
                $projectId = $projects->first()->id;
                return $this->redirect(['action' => 'submit', 'id' => $projectId]);
            }
            if (count($projects) == 0) {
                $this->Flash->error('You do not currently have any projects that are eligible for submitting a report.');
                return $this->redirect($this->getRequest()->referer() ?? '/');;
            }
            $this->title('Submit report');
            $this->set(compact('projects'));
            $this->viewBuilder()->setTemplate('select_project');
            $this->setCurrentBreadcrumb('Select a project');
            return;
        }

        $projectsTable = TableRegistry::getTableLocator()->get('Projects');
        $project = $projectsTable->getNotDeleted($projectId);
        if (!$this->isOwnProject($projectId)) {
            $this->Flash->error('Project not found');
            $this->setResponse($this->getResponse()->withStatus(404));
            return $this->redirect($this->getRequest()->referer() ?? '/');
        }
        if ($project->is_finalized) {
            $this->Flash->error('New reports cannot be submitted for finalized projects.');
            $this->setResponse($this->getResponse()->withStatus(400));
            return $this->redirect($this->getRequest()->referer() ?? '/');
        }

        $report = $this->Reports->newEmptyEntity();
        $report->user_id = $user->id;
        $report->project_id = $projectId;
        $submittingReport = $this->request->is('post') && !$this->request->getQuery('selectingProject');
        if ($submittingReport) {
            $report = $this->Reports->patchEntity($report, $this->request->getData());
            if ($this->Reports->save($report)) {
                $this->Flash->success(__('Report submitted. Thanks for keeping us updated!'));

                $back = $this->request->getQuery('back') ?? Router::url([
                    'controller' => 'Projects',
                    'action' => 'index',
                ]);

                return $this->redirect($back);
            }
            $this->Flash->error(
                'The report could not be submitted. Please check for errors and try again, and '
                . '<a href="/contact">contact us</a> if you need assistance.',
                ['escape' => false]
            );
        }

        $this->viewBuilder()->setTemplate('form');
        $this->set(compact('report', 'project'));
        $this->addBreadcrumbForProject($project, 'My');
    }

    /**
     * Edit method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit()
    {
        $reportId = $this->request->getParam('id');
        $report = $this->Reports->get($reportId, [
            'contain' => ['Projects'],
        ]);

        if (!$this->isOwnProject($report->project_id)) {
            $this->Flash->error('Sorry, but you are not authorized to access that project.');
            $this->setResponse($this->getResponse()->withStatus(403));
            return $this->redirect('/');
        }

        if ($report->project->isDeleted()) {
            $this->Flash->error('Project not found');
            $this->setResponse($this->getResponse()->withStatus(404));
            return $this->redirect('/');
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $report = $this->Reports->patchEntity($report, $this->request->getData());
            if ($this->Reports->save($report)) {
                $this->Flash->success(__('The report has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(
                'The report could not be saved. ' . $this->errorTryAgainContactMsg,
                ['escape' => false]
            );
        }
        $users = $this->Reports->Users->find('list', ['limit' => 200])->all();
        $projects = $this->Reports->Projects->find('list', ['limit' => 200])->all();
        $this->set(compact('report', 'users', 'projects'));
        $this->viewBuilder()->setTemplate('form');

        $this->addBreadcrumbForProject($report->project, 'My');
        $this->addBreadcrumb(
            $report->created->format('F j, Y'),
            [
                'action' => 'view',
                'id' => $reportId,
            ]
        );
        $this->setCurrentBreadcrumb('Edit');
    }

    /**
     * Delete method
     *
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete()
    {
        $id = $this->request->getParam('id');
        $this->request->allowMethod(['post', 'delete']);
        $report = $this->Reports->get($id);

        if (!$this->isOwnProject($report->project_id)) {
            $this->Flash->error('Sorry, but you are not authorized to access that project.');
            $this->setResponse($this->getResponse()->withStatus(403));
            return $this->redirect('/');
        }

        if ($this->Reports->delete($report)) {
            $this->Flash->success(__('The report has been deleted.'));
        } else {
            $this->Flash->error(
                'The report could not be deleted. ' . $this->errorTryAgainContactMsg,
                ['escape' => false]
            );
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * @return void
     */
    public function project(): void
    {
        $projectId = $this->request->getParam('id');
        $referredFromMyProjects = (bool)$this->getRequest()->getQuery('myProjects');
        $reports = $this->Reports
            ->find()
            ->where(['Reports.project_id' => $projectId])
            ->orderDesc('Reports.created')
            ->contain(['Projects'])
            ->all();
        $this->set(compact('reports'));
        $projectsTable = TableRegistry::getTableLocator()->get('Projects');
        $project = $projectsTable->getNotDeleted($projectId);
        $this->title($project->title);

        $this->addBreadcrumbForProject($project, $referredFromMyProjects ? 'My' : null);
        $this->setCurrentBreadcrumb('Reports');
    }
}
