<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Application;
use App\Model\Entity\Project;
use App\Model\Entity\Transaction;
use Cake\Event\EventInterface;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Transactions Controller
 *
 * @property \App\Model\Table\TransactionsTable $Transactions
 * @method Transaction[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TransactionsController extends AdminController
{
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->addControllerBreadcrumb();
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->title('Transactions');
        $this->paginate = [
            'contain' => ['Projects'],
            'order' => ['Transactions.date DESC'],
        ];
        $transactions = $this->paginate($this->Transactions);

        $this->set(compact('transactions'));
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
        $transaction = $this->Transactions->get($id, [
            'contain' => ['Projects'],
        ]);

        $title = 'Transaction ' . $transaction->id;
        $this->title($title);
        $this->setCurrentBreadcrumb($title);
        $this->set(compact('transaction'));
    }

    /**
     * Add method
     *
     * Loads a React app that uses \App\Controller\Api\TransactionsController to submit the form
     *
     * @return void
     */
    public function add(): void
    {
        $transaction = $this->Transactions->newEmptyEntity();
        if (!$transaction->date) {
            $transaction->date = new FrozenTime('now', Application::LOCAL_TIMEZONE);
        }

        $this->title('Add Transaction');
        $this->viewBuilder()->setTemplate('form');
        $this->setProjectsInCycles();
        $this->set([
            'toLoad' => $this->getAppFiles('transaction-form/dist/assets'),
            'transaction' => $transaction,
        ]);
    }

    private function setProjectsInCycles()
    {
        $cyclesTable = TableRegistry::getTableLocator()->get('FundingCycles');
        $cycles = $cyclesTable
            ->find()
            ->select(['id', 'vote_end']) // vote_end needed to determine the cycle's name
            ->orderAsc('application_begin')
            ->toArray();
        $cyclesRetval = [];
        foreach ($cycles as $cycle) {
            $cyclesRetval[$cycle['id']] = ['name' => $cycle->name];
        }

        $projectsTable = TableRegistry::getTableLocator()->get('Projects');
        $projects = $projectsTable
            ->find('notDeleted')
            ->find('notFinalized')
            ->select(['Projects.id', 'Projects.title', 'Projects.funding_cycle_id'])
            ->contain([
                'Users' => function (Query $query) {
                    return $query->select(['Users.id', 'Users.name']);
                }
            ])
            ->orderAsc('Projects.title');
        foreach ($projects as $project) {
            $cyclesRetval[$project->funding_cycle_id]['projects'][] = [
                'id' => $project->id,
                'title' => sprintf(
                    '%s (%s, #%s)',
                    $project->title,
                    $project->user->name,
                    $project->id,
                ),
            ];
        }

        $this->set(['cycles' => $cyclesRetval]);
    }

    private function setupForm(): void
    {
        $projects = $this->Transactions->Projects
            ->find('notDeleted')
            ->find('notFinalized')
            ->select([
                'Projects.id',
                'Projects.title'
            ])
            ->contain([
                'Users' => function (Query $query) {
                    return $query->select(['Users.id', 'Users.name']);
                }
            ])
            ->orderAsc('title')
            ->toArray();
        $projects = Hash::combine($projects, '{n}.id', '{n}');
        $prefixedProjects = array_map(function (Project $project) {
            return $project->user->name . ': ' . $project->title;
        }, $projects);
        asort($prefixedProjects);

        $this->viewBuilder()->setTemplate('form');
        $this->set([
            'projects' => $prefixedProjects,
        ]);
    }

    /**
     * Edit method
     *
     * Loads a React app that uses \App\Controller\Api\TransactionsController to submit the form
     *
     * @return void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit(): void
    {
        $id = $this->request->getParam('id');
        $transaction = $this->Transactions->get($id);

        // Convert cents to dollars
        $transaction->amount_net /= 100;
        $transaction->amount_gross /= 100;

        $title = 'Update transaction ' . $transaction->id;
        $this->title($title);
        $this->setCurrentBreadcrumb($title);
        $this->viewBuilder()->setTemplate('form');
        $this->setProjectsInCycles();
        $this->set([
            'toLoad' => $this->getAppFiles('transaction-form/dist/assets'),
            'transaction' => $transaction,
        ]);
    }
}
