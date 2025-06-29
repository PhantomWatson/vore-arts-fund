<?php

namespace App\Controller\My;

use App\Alert\Alert;
use App\Model\Entity\Project;
use App\Model\Entity\Transaction;
use App\Model\Table\TransactionsTable;
use App\SecretHandler\SecretHandler;
use Cake\Event\EventInterface;
use Cake\Http\Exception\InternalErrorException;
use Cake\Http\Response;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

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
        $this->title(sprintf('Loan #%s (%s)', $project->id, $project->title));
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
        $expectedTotal = ceil(
            ($amountTowardLoan + Transaction::STRIPE_FEE_FIXED)
            / (1 - Transaction::STRIPE_FEE_PERCENTAGE)
        );
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
        $this->set(compact('totalAmount', 'projectId', 'name'));
    }

    public function paymentComplete()
    {
        $this->Flash->success('Thank you for your repayment! A receipt will be emailed to you shortly. It may take a few minutes for this payment to be reflected in your balance below.');
        return $this->redirect(['action' => 'index']);
    }

    public function loanAgreement()
    {
        $projectId = $this->getRequest()->getParam('id');
        $project = $this->Projects->getNotDeleted($projectId, ['contain' => 'Users']);

        $this->title('Loan Agreement');
        $this->addBreadcrumb($project->title, []);

        if ($project->isAgreeable()) {
            return $this->redirect(['action' => 'verifyCheckDetails', 'id' => $projectId]);
        }
        if ($project->loan_agreement_date) {
            return $this->redirect(['action' => 'viewLoanAgreement', 'id' => $projectId]);
        }
        throw new InternalErrorException('Loan agreement is not signed nor signable but auth checks passed.');
    }

    public function viewLoanAgreement()
    {
        $projectId = $this->getRequest()->getParam('id');
        $project = $this->Projects->getNotDeleted($projectId, ['contain' => 'Users']);
        $this->title('Loan Agreement');
        $this->addBreadcrumb($project->title, []);
        $this->set(compact('project'));
    }

    /**
     * @param Project $project
     * @return bool
     */
    private function saveLoanAgreement(Project $project)
    {
        $project->loan_agreement_signature = $this->request->getData('loan_agreement_signature');
        $project->loan_agreement_date = new \DateTime();
        $project->loan_due_date = new \DateTime(\App\Model\Entity\Project::DUE_DATE);
        $project->loan_agreement_version = Project::getLatestTermsVersion();

        // Double-check that signature is non-blank
        $success = $project->loan_agreement_signature && (bool)$this->Projects->save($project);

        // Send alert
        $projectName = "project #{$project->id} ({$project->title})";
        $alert = new Alert();
        if ($success) {
            $alert->addLine("Loan agreement submitted for $projectName");
            $alert->addLine(sprintf(
                'Time to send a check and <%s|record the disbursement>',
                Router::url([
                    'prefix' => 'Admin',
                    'controller' => 'Transactions',
                    'action' => 'add',
                ], true),
            ));
            $alert->send(Alert::TYPE_APPLICATIONS);
        } else {
            $alert->addLine("Failure to save loan agreement for $projectName");
            $alert->addLine('Error submitting loan agreement for ' . $projectName);
            $alert->addLine('Project data:');
            $alert->addLine('```' . print_r($project, true) . '```');
            $alert->addLine('Entity errors:');
            $alert->addLine('```' . print_r($project->getErrors(), true) . '```');
            $alert->send(Alert::TYPE_ERRORS);
        }

        return $success;
    }

    /**
     * @throws \SodiumException
     * @return void
     */
    public function signLoanAgreement()
    {
        $projectId = $this->getRequest()->getParam('id');
        $project = $this->Projects->getNotDeleted($projectId, ['contain' => 'Users']);

        if ($project->loan_agreement_date) {
            $this->redirect(['action' => 'viewLoanAgreement', 'id' => $projectId]);
        }

        $this->title('Loan Agreement');
        $this->addBreadcrumb($project->title, []);
        $this->set(compact('project'));

        if ($this->getRequest()->is('get')) {
            return;
        }

        if ($project->requires_tin) {
            $tin = $this->getValidatedTin();

            // Remove sensitive values from the request data
            $data = $this->request->getData();
            unset($data['tin_provide']);
            unset($data['tin_confirm']);
            $this->request = $this->request->withParsedBody($data);
            unset($_POST);

            if (!$tin) {
                return;
            }

            $tinSaveSuccess = $this->storeTin($project, $tin);
            sodium_memzero($tin);

            $loanAgreementSaveSuccess = $tinSaveSuccess && $this->saveLoanAgreement($project);
        } else {
            $loanAgreementSaveSuccess = $this->saveLoanAgreement($project);
        }

        if ($loanAgreementSaveSuccess) {
            $this->Flash->success(
                'Loan agreement signed. The Vore Arts Fund staff has been notified, and you should expect an email confirmation that your check is in the mail in the next few days. You are encouraged to save this agreement in your records (we suggest printing to a PDF file), but this agreement will remain available for you to access through the My Loans page on this website.'
            );
            $this->redirect(['action' => 'viewLoanAgreement', 'id' => $projectId]);
        } else {
            $this->Flash->error(
                'There was an error submitting your loan agreement, and our website support staff has been notified. '
                . $this->errorTryAgainContactMsg,
                ['escape' => false]
            );
        }
    }

    public function verifyCheckDetails()
    {
        $projectId = $this->getRequest()->getParam('id');
        $project = $this->Projects->getNotDeleted($projectId, ['contain' => 'Users']);

        if (!$project->isAgreeable()) {
            $this->Flash->error('A loan agreement cannot be signed for this project at this time.');
            $this->setResponse($this->getResponse()->withStatus(404));
            return $this->redirect([
                'prefix' => 'My',
                'controller' => 'Loans',
                'action' => 'index',
            ]);
        }

        if (!$this->getRequest()->is('get')) {
            $this->Projects->patchEntity(
                $project,
                $this->request->getData(),
                ['fields' => ['check_name', 'address', 'zipcode']]
            );

            if ($this->Projects->save($project)) {
                return $this->redirect(['action' => 'signLoanAgreement', 'id' => $projectId]);
            }
            $this->Flash->error(
                'There was an error submitting your check details. ' . $this->errorTryAgainContactMsg,
                ['escape' => false]
            );
        }

        $this->title('Verify Check Details');
        $this->addBreadcrumb($project->title, []);
        $this->set(compact('project'));
        return null;
    }

    /**
     * Returns a tax ID number, or FALSE if validation fails
     *
     * Adds Flash error messages if appropriate
     *
     * @todo Add specific SSN/EIN regex validation
     * @return string|false
     * @throws \SodiumException
     */
    private function getValidatedTin()
    {
        $tin = $this->getRequest()->getData('tin_provide');
        $tinConfirm = $this->getRequest()->getData('tin_confirm');

        if (!$tin) {
            $this->Flash->error('Tax ID number required.');
            sodium_memzero($tinConfirm);
            return false;
        }
        if ($tin != $tinConfirm) {
            $this->Flash->error('Tax ID numbers did not match');
            sodium_memzero($tin);
            sodium_memzero($tinConfirm);
            return false;
        }
        return $tin;
    }

    /**
     * Attempts to save an encrypted tax ID number to the provided project
     *
     * @param Project $project
     * @param string $tin
     * @return bool
     * @throws \SodiumException
     */
    private function storeTin(Project $project, string $tin)
    {
        $tinSaveSuccess = false;
        $projectName = "project #{$project->id} ({$project->title})";
        $secretHandler = new SecretHandler();
        try {
            $tinSaveSuccess = (bool)$secretHandler->setTin($project->id, $tin);
        } catch (\SodiumException $e) {
            $alert = new Alert();
            $alert->addLine(
                '\SodiumException thrown when trying to save encrypted tax ID number for ' . $projectName . ':'
            );
            $alert->addLine($e->getMessage());
            $alert->send(Alert::TYPE_APPLICATIONS);
        }
        if (!$tinSaveSuccess) {
            $alert = new Alert();
            $alert->addLine('Failed save encrypted tax ID number for ' . $projectName);
            $alert->send(Alert::TYPE_APPLICATIONS);
        }
        sodium_memzero($tin);

        return $tinSaveSuccess;
    }
}
