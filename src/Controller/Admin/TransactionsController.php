<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Application;
use App\Model\Entity\Project;
use Cake\Event\EventInterface;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
use Cake\ORM\Query;
use Cake\Utility\Hash;

/**
 * Transactions Controller
 *
 * @property \App\Model\Table\TransactionsTable $Transactions
 * @method \App\Model\Entity\Transaction[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
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
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $transaction = $this->Transactions->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $user = $this->getAuthUser();
            $data['user_id'] = $user?->id;
            $data['date'] = FundingCyclesController::convertTimeToUtc($data['date']);

            // Convert dollars to cents
            $data['amount_gross'] *= 100;
            $data['amount_net'] *= 100;

            $transaction = $this->Transactions->patchEntity($transaction, $data);
            if ($this->Transactions->save($transaction)) {
                $this->Flash->success(__('The transaction has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The transaction could not be saved. Please, try again.'));
        }

        if (!$transaction->date) {
            $transaction->date = new FrozenTime('now', Application::LOCAL_TIMEZONE);
        }

        $this->title('Add Transaction');
        $this->set([
            'transaction' => $transaction,
        ]);
        $this->setupForm();
    }

    private function setupForm(): void
    {
        $projects = $this->Transactions->Projects
            ->find()
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
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit()
    {
        $id = $this->request->getParam('id');
        $transaction = $this->Transactions->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            // Convert dollars to cents
            $data['amount_net'] *= 100;
            $data['amount_gross'] *= 100;

            $transaction = $this->Transactions->patchEntity($transaction, $data);
            if ($this->Transactions->save($transaction)) {
                $this->Flash->success(__('The transaction has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The transaction could not be saved. Please, try again.'));
        }

        // Convert cents to dollars
        $transaction->amount_net /= 100;
        $transaction->amount_gross /= 100;

        $title = 'Update transaction ' . $transaction->id;
        $this->setCurrentBreadcrumb($title);
        $this->title($title);
        $this->set(compact('transaction'));
        $this->setupForm();
    }

    /**
     * Delete method
     *
     * @return \Cake\Http\Response|null|void Redirects to index
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found
     */
    public function delete()
    {
        $id = $this->request->getParam('id');
        $this->request->allowMethod(['post', 'delete']);
        $report = $this->Transactions->get($id);
        if ($this->Transactions->delete($report)) {
            $this->Flash->success('The transaction has been deleted');
        } else {
            $this->Flash->error('The transaction could not be deleted');
        }

        return $this->redirect(['action' => 'index']);
    }
}
