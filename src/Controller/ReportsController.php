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
