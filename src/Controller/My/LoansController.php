<?php

namespace App\Controller\My;

use App\Alert\Alert;
use App\Model\Entity\Project;
use App\Model\Entity\Transaction;
use App\Model\Table\TransactionsTable;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

/**
 * LoansController
 *
 * Handles loan-related actions for the user's projects.
 *
 * @property \App\Model\Table\ProjectsTable $Projects
 */
class LoansController extends \App\Controller\AppController
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
            $this->Flash->error('Loan not found');
            $this->setResponse($this->getResponse()->withStatus(404));
            return $this->redirect('/');
        }

        $this->Projects = $this->fetchTable('Projects');

        $this->addControllerBreadcrumb('My Loans');

        return null;
    }

    public function view()
    {
        $projectId = $this->getRequest()->getParam('id');
        $project = $this->Projects->getNotDeleted($projectId);
        if (!$project || $project->status_id !== Project::STATUS_AWARDED_AND_DISBURSED) {
            $this->Flash->error('Loan not found');
            return $this->redirect(['action' => 'index']);
        }
        /** @var TransactionsTable $transactionsTable */
        $transactionsTable = TableRegistry::getTableLocator()->get('Transactions');
        $repayments = $transactionsTable->getRepaymentsForProject($projectId);
        $this->title('Loan for ' . $project->title);
        $this->set(compact('project', 'repayments'));
    }

    public function index()
    {
        $userId = $this->getAuthUser()->id;
        $projects = $this->Projects
            ->find('notDeleted')
            ->where([
                'Projects.user_id' => $userId,
                'Projects.status_id' => Project::STATUS_AWARDED_AND_DISBURSED,
            ])
            ->order(['Projects.created' => 'DESC'])
            ->all();

        $this->title('My Loans');
        $this->set(compact('projects'));
    }

    public function payment()
    {
        $projectId = $this->getRequest()->getParam('id');
        $project = $this->Projects->getNotDeleted($projectId);
        if (!$project || $project->status_id !== Project::STATUS_AWARDED_AND_DISBURSED) {
            $this->Flash->error('Loan not found');
            return $this->redirect(['action' => 'index']);
        }

        /** @var TransactionsTable $transactionsTable */
        $transactionsTable = TableRegistry::getTableLocator()->get('Transactions');
        $repayments = $transactionsTable->getRepaymentsForProject($projectId);

        $this->title('Repay loan: ' . $project->title);
        $this->set(compact('project', 'repayments'));
        $this->set([
            'toLoad' => $this->getAppFiles('repayment-form/dist/assets'),
        ]);

        return null;
    }

    public function paymentProcess()
    {
        $this->getRequest()->allowMethod('post');

        $projectId = $this->getRequest()->getParam('id');
        $project = $this->Projects->getNotDeleted($projectId);
        if (!$project || $project->status_id !== Project::STATUS_AWARDED_AND_DISBURSED) {
            $this->Flash->error('Loan not found');
            return $this->redirect(['action' => 'index']);
        }

        $amountTowardLoan = (float)$this->getRequest()->getData('amountTowardBalance') * 100; // in cents
        $totalAmount = (float)$this->getRequest()->getData('total') * 100; // in cents
        if (!$totalAmount) {
            throw new \Cake\Http\Exception\BadRequestException('No amount provided');
        }

        // Throw error if loan is already paid off
        if ($project->getBalance() <= 0) {
            $this->Flash->warning('This loan is already paid off.');
            return $this->redirect(['action' => 'index']);
        }

        // If expected and actual total are off by more than one cent, log error and send alert
        $expectedTotal = $amountTowardLoan
            + ($amountTowardLoan * Transaction::STRIPE_FEE_PERCENTAGE)
            + Transaction::STRIPE_FEE_FIXED;
        if (abs($expectedTotal - $totalAmount) > 1) {
            Log::write(
                LOG_ERR,
                "Discrepancy in expected ($expectedTotal) and actual ($totalAmount) loan repayment amount",
                ['scope' => 'stripe']
            );
            $alert = new Alert();
            $alert->addLine('Discrepancy in expected/actual loan repayment amount');
            $alert->addList([
                'User ID: ' . $this->getAuthUser()->id,
                'Project ID: ' . $projectId,
                'Amount toward loan in POST data: ' . $amountTowardLoan,
                'Total amount in POST data: ' . $totalAmount,
                'Expected total (amount toward loan + fees): ' . $expectedTotal,
            ]);
            $alert->addLine('This could indicate bad processing fee math or tampering with the form data.');
            $alert->send(Alert::TYPE_TRANSACTIONS);
        }

        // Send total to view for Stripe
        $this->title('Repay loan: ' . $project->title);
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $user = $usersTable->get($this->getAuthUser()->id);
        $name = $user->name;
        $this->set(compact('amountTowardLoan', 'totalAmount', 'projectId', 'name'));
    }

    public function paymentComplete()
    {
        $this->Flash->success('Thank you for your repayment! A receipt will be emailed to you shortly.');
        return $this->redirect(['action' => 'index']);
    }
}
