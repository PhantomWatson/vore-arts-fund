<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Exception\ForbiddenException;
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
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users', 'Applications'],
        ];
        $reports = $this->paginate($this->Reports);

        $this->set(compact('reports'));
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
            'contain' => ['Users', 'Applications'],
        ]);

        $this->set(compact('report'));
    }

    /**
     * Submit method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function submit($applicationId = null)
    {
        if (!$this->isOwnApplication($applicationId)) {
            throw new NotFoundException();
        }
        $report = $this->Reports->newEmptyEntity();
        /** @var \App\Model\Entity\User $user */
        $user = $this->Authentication->getIdentity();
        $report->user_id = $user->id;
        $report->application_id = $applicationId;
        if ($this->request->is('post')) {
            $report = $this->Reports->patchEntity($report, $this->request->getData());
            if ($this->Reports->save($report)) {
                $this->Flash->success(__('Report submitted. Thanks for keeping us updated!'));

                $back = $this->request->getQuery('back') ?? Router::url([
                    'controller' => 'Applications',
                    'action' => 'index',
                ]);

                return $this->redirect($back);
            }
            $this->Flash->error(__('The report could not be submitted. Please check for errors and try again.'));
        }
        $applicationsTable = TableRegistry::getTableLocator()->get('Applications');
        $application = $applicationsTable->get($applicationId);
        $this->set(compact('report', 'application'));
        $this->viewBuilder()->setTemplate('form');
        $this->title('Submit report for ' . $application->title);
    }

    /**
     * Edit method
     *
     * @param string|null $id Report id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $report = $this->Reports->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $report = $this->Reports->patchEntity($report, $this->request->getData());
            if ($this->Reports->save($report)) {
                $this->Flash->success(__('The report has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The report could not be saved. Please, try again.'));
        }
        $users = $this->Reports->Users->find('list', ['limit' => 200])->all();
        $applications = $this->Reports->Applications->find('list', ['limit' => 200])->all();
        $this->set(compact('report', 'users', 'applications'));
        $this->viewBuilder()->setTemplate('form');
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
            $this->Flash->error(__('The report could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * @param $applicationId
     * @return void
     */
    public function application($applicationId = null): void
    {
        $reports = $this->Reports
            ->find()
            ->where(['Reports.application_id' => $applicationId])
            ->orderDesc('Reports.created')
            ->contain(['Applications'])
            ->all();
        $this->set(compact('reports'));
        $applicationsTable = TableRegistry::getTableLocator()->get('Applications');
        $application = $applicationsTable->get($applicationId);
        $this->title('Reports for ' . $application->title);
    }
}
