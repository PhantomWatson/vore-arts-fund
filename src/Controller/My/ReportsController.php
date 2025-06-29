<?php
declare(strict_types=1);

namespace App\Controller\My;

use App\Controller\AppController;
use App\Model\Entity\Project;
use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\ORM\Query;
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

    /**
     * @param EventInterface $event
     * @return Response|null
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        if (!$this->getAuthUser()) {
            return $this->redirect(\App\Application::LOGIN_URL);
        }

        $projectId = $this->request->getParam('id');
        if ($projectId && !$this->isOwnProject($projectId)) {
            $this->Flash->error('Project not found');
            $this->setResponse($this->getResponse()->withStatus(404));
            return $this->redirect('/');
        }

        $this->addControllerBreadcrumb('My Reports');

        return null;
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $projectsTable = TableRegistry::getTableLocator()->get('Projects');
        $projects = $projectsTable
            ->find('notDeleted')
            ->where([
                'Projects.user_id' => $this->getAuthUser()->id,
                'Projects.status_id IN' => [
                    Project::STATUS_AWARDED_NOT_YET_DISBURSED,
                    Project::STATUS_AWARDED_AND_DISBURSED
                ],
            ])
            ->contain([
                'FundingCycles',
                'Reports' => function (Query $q) {
                    return $q->orderDesc('Reports.created');
                }
            ])
            ->order(['Projects.created' => 'DESC'])
            ->all();
        $this->set(compact('projects'));
        $this->setCurrentBreadcrumb('My Reports');
        $this->title('My Reports');
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
        $submittingReport = $this->request->is('post') && !$this->request->getQuery('selectingProject');
        if ($submittingReport) {
            $report->user_id = $user->id;
            $report->project_id = $projectId;
            $report->body = $this->getRequest()->getData('body');
            $report->is_final = (bool)$this->getRequest()->getData('is_final');
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
                . '<a href="/contact">contact us</a> if you need assistance. Details: ' . $this->getEntityErrorDetails($report),
                ['escape' => false]
            );
        }

        $this->viewBuilder()->setTemplate('form');
        $this->set(compact('report', 'project'));
        $this->addBreadcrumb(
            $project->title,
            [
                'prefix' => 'My',
                'controller' => 'Projects',
                'action' => 'view',
                'id' => $project->id,
            ]
        );
        $this->title('New report');
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

        $this->addBreadcrumb(
            $report->project->title,
            [
                'prefix' => 'My',
                'controller' => 'Projects',
                'action' => 'view',
                'id' => $report->project->id,
            ]
        );
        $this->title('New report');
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
}
