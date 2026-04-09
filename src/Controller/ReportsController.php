<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;

/**
 * Reports Controller
 *
 * @property \App\Model\Table\ReportsTable $Reports
 * @method \App\Model\Entity\Report[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ReportsController extends AppController
{
    public array $paginate = [
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

        $this->Authentication->allowUnauthenticated([
            'index',
            'project',
            'view',
        ]);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Reports->find()->contain(['Users', 'Projects']);
        $reports = $this->paginate($query);

        $this->title('Project Reports');
        $this->set(compact('reports'));
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
        $report = $this->Reports->get($id, contain: ['Users', 'Projects']);
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
        $this->setCurrentBreadcrumb($report->created->setTimezone(\App\Application::LOCAL_TIMEZONE)->format('F j, Y'));

        $this->set(compact('report'));
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
            ->orderByDesc('Reports.created')
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
