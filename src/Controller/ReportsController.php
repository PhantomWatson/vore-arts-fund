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

    public function beforeFilter(EventInterface $event): void
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
     * @param string|null $id Report id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
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
                $report->project->id,
            ]
        );
        $this->setCurrentBreadcrumb($report->created->format('F j, Y'));

        $this->set(compact('report'));
    }

    /**
     * @param Project $project
     * @return void
     */
    private function addBreadcrumbForProject(Project $project): void
    {
        $this->addBreadcrumb(
            'Projects',
            [
                'controller' => 'Projects',
                'action' => 'index',
            ]
        );
        $this->addBreadcrumb(
            $project->title,
            [
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
    public function submit($projectId = null)
    {
        if (!$this->isOwnProject($projectId)) {
            throw new NotFoundException();
        }
        $report = $this->Reports->newEmptyEntity();
        $user = $this->getAuthUser();
        $report->user_id = $user->id;
        $report->project_id = $projectId;
        if ($this->request->is('post')) {
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
        $projectsTable = TableRegistry::getTableLocator()->get('Projects');
        $project = $projectsTable->get($projectId);
        $this->set(compact('report', 'project'));
        $this->viewBuilder()->setTemplate('form');
        $this->title('Submit report for ' . $project->title);

        $this->addBreadcrumbForProject($project);
        $this->setCurrentBreadcrumb('Submit');
    }

    /**
     * Edit method
     *
     * @param string|null $reportId Report id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($reportId = null)
    {
        $report = $this->Reports->get($reportId, [
            'contain' => ['Projects'],
        ]);
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

        $this->addBreadcrumbForProject($report->project);
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
     * @param string|null $id Report id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $report = $this->Reports->get($id);
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
     * @param $projectId
     * @return void
     */
    public function project($projectId = null): void
    {
        $reports = $this->Reports
            ->find()
            ->where(['Reports.project_id' => $projectId])
            ->orderDesc('Reports.created')
            ->contain(['Projects'])
            ->all();
        $this->set(compact('reports'));
        $projectsTable = TableRegistry::getTableLocator()->get('Projects');
        $project = $projectsTable->get($projectId);
        $this->title($project->title);

        $this->addBreadcrumbForProject($project);
        $this->setCurrentBreadcrumb('Reports');
    }
}
