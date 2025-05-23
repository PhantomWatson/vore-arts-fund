<?php
declare(strict_types=1);

namespace App\Controller\My;

use App\Alert\Alert;
use App\Controller\ProjectsController as BaseProjectsController;
use App\Model\Entity\Note;
use App\Model\Entity\Project;
use App\Model\Table\NotesTable;
use App\SecretHandler\SecretHandler;
use Cake\Event\EventInterface;
use Cake\Http\Exception\InternalErrorException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

/**
 * ProjectsController
 *
 * @property \App\Model\Table\ProjectsTable $Projects
 * @property \App\Model\Table\CategoriesTable $Categories
 * @property \App\Model\Table\FundingCyclesTable $FundingCycles
 * @property \App\Model\Table\ImagesTable $Images
 */
class ProjectsController extends BaseProjectsController
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
            $this->Flash->error('Sorry, but you are not authorized to access that project.');
            $this->setResponse($this->getResponse()->withStatus(403));
            return $this->redirect('/');
        }

        $this->Projects = $this->fetchTable('Projects');

        $this->addControllerBreadcrumb('My Projects');

        return null;
    }

    /**
     * Page for viewing one's own project
     *
     * @return \Cake\Http\Response|null
     */
    public function view(): ?Response
    {
        return $this->_view();
    }

    /**
     * Page for withdrawing an application from consideration
     *
     * @return Response
     */
    public function withdraw()
    {
        $this->getRequest()->allowMethod('post');
        $id = $this->request->getParam('id');
        if ($this->Projects->updateStatus($id, Project::STATUS_WITHDRAWN)) {
            $this->Flash->success('Application withdrawn.');
        } else {
            $this->Flash->error('There was an error withdrawing your application.');
        }
        return $this->redirectToIndex();
    }

    /**
     * Page for updating a draft or (re)submitting an application
     *
     * @return \Cake\Http\Response|null
     */
    public function edit(): ?Response
    {
        // Confirm project can be updated
        $projectId = $this->request->getParam('id');
        /** @var Project $project */
        $project = $this->Projects->getForForm($projectId);
        if (!$project->isUpdatable()) {
            $this->Flash->error(
                'That application cannot currently be updated. ' . $this->errorContactMsg,
                ['escape' => false]
            );
            return $this->redirect('/');
        }

        // Set up view vars
        $this->title('Update and Submit');
        $this->viewBuilder()->setTemplate('/Projects/form');
        $this->setFromNow($project->getSubmitDeadline());
        $this->setProjectVars();
        $this->set('toLoad', $this->getAppFiles('image-uploader/dist', 'image-uploader/dist/styles'));

        // Process form
        if ($this->request->is('put')) {
            $data = $this->request->getData();

            // If saving, status doesn't change. Otherwise, it's submitted for review.
            $submittingForReview = ($data['save-mode'] ?? null) == 'submit';
            if ($submittingForReview) {
                $project->status_id = Project::STATUS_UNDER_REVIEW;
            }

            if ($this->processProject($project, $data)) {
                return $this->redirectToIndex();
            }
        } else {
            $user = $this->getAuthUser();
            $project->address = $user->address;
            $project->zipcode = $user->zipcode;
        }

        $this->set(compact('project'));

        return null;
    }

    /**
     * Page for marking an application as deleted
     *
     * @return Response
     */
    public function delete()
    {
        $id = $this->request->getParam('id');
        $project = $this->Projects->getNotDeleted($id);
        if ($this->request->is(['delete', 'post']) && $this->Projects->markDeleted($project)) {
            $this->Flash->success('Application has been deleted');
        } else {
            $this->Flash->error(
                'There was an error deleting that application. ' . $this->errorTryAgainContactMsg,
                ['escape' => false]
            );
        }
        return $this->redirect($this->referer());
    }

    public function index()
    {
        $this->title('My Projects');
        $user = $this->getAuthUser();
        $projects = $this->Projects
            ->find('notDeleted')
            ->where(['user_id' => $user->id])
            ->orderDesc('Projects.created')
            ->contain([
                'FundingCycles',
                'Reports' => function (Query $q) {
                    return $q->select([
                        'Reports.project_id',
                        'Reports.id',
                    ]);
                },
                'Notes' => function (Query $q) {
                    return $q
                        ->find('notInternal')
                        ->select([
                            'Notes.project_id',
                            'Notes.id'
                        ]);
                }
            ])
            ->all();
        $this->set(compact('projects'));
    }

    /**
     * Shows the applicant all of the non-internal notes (a.k.a. messages) for the selected project
     *
     * @return Response|null
     */
    public function messages()
    {
        $projectId = $this->request->getParam('id');
        if (!$projectId) {
            $this->Flash->error('Invalid project selected');
            return $this->redirectToIndex();
        }

        /** @var Project $project */
        $project = $this->Projects->getNotDeleted($projectId);

        /** @var NotesTable $notesTable */
        $notesTable = TableRegistry::getTableLocator()->get('Notes');
        $notes = $notesTable
            ->find('notInternal')
            ->where(['Notes.project_id' => $projectId])
            ->orderDesc('Notes.created')
            ->all();

        $this->set(compact('project', 'notes'));

        $this->title('Messages: ' . $project->title);
        $this->addBreadcrumb($project->title, [
            'prefix' => 'My',
            'controller' => 'Projects',
            'action' => 'view',
            'id' => $projectId,
        ]);
        $this->setCurrentBreadcrumb('Messages');

        return null;
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
        $this->addBreadcrumb($project->title, [
            'prefix' => 'My',
            'controller' => 'Projects',
            'action' => 'view',
            'id' => $projectId,
        ]);
        $this->set(compact('project'));
    }

    private function redirectToIndex(): ?Response
    {
        return $this->redirect([
            'prefix' => 'My',
            'controller' => 'Projects',
            'action' => 'index',
        ]);
    }

    public function verifyCheckDetails()
    {
        $projectId = $this->getRequest()->getParam('id');
        $project = $this->Projects->getNotDeleted($projectId, ['contain' => 'Users']);

        if (!$project->isAgreeable()) {
            $this->Flash->error('A loan agreement cannot be signed for this project at this time.');
            $this->setResponse($this->getResponse()->withStatus(404));
            return $this->redirectToIndex();
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
        $this->addBreadcrumb($project->title, [
            'prefix' => 'My',
            'controller' => 'Projects',
            'action' => 'view',
            'id' => $projectId,
        ]);
        $this->set(compact('project'));
        return null;
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
        $this->addBreadcrumb($project->title, [
            'prefix' => 'My',
            'controller' => 'Projects',
            'action' => 'view',
            'id' => $projectId,
        ]);
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
                'Loan agreement signed. The Vore Arts Fund staff has been notified, and you should expect an email confirmation that your check is in the mail in the next few days. You are encouraged to save this agreement in your records (we suggest printing to a PDF file), but this agreement will remain available for you to access through this website.'
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

    public function sendMessage(): Response
    {
        $projectId = $this->request->getParam('id');
        $project = $this->Projects->getNotDeleted($projectId);
        $noteBody = $this->request->getData('body');
        if (!$noteBody) {
            $this->Flash->error('Message body is required.');
            return $this->redirect(['action' => 'messages', 'id' => $projectId]);
        }

        $user = $this->getAuthUser();
        if ($project->user_id != $user->id) {
            throw new NotFoundException('Invalid project selected');
        }

        /** @var NotesTable $notesTable */
        $notesTable = $this->fetchTable('Notes');
        $message = $notesTable->newEntity([
            'type' => Note::TYPE_MESSAGE_FROM_APPLICANT,
            'body' => $noteBody,
            'user_id' => $user->id,
            'project_id' => $project->id,
        ]);

        if ($notesTable->save($message)) {
            $this->Flash->success('Message sent');
        } else {
            $this->Flash->error(
                'Error sending message'
                . 'Details: ' . $this->getEntityErrorDetails($message)
            );
        }

        return $this->redirect(['action' => 'messages', 'id' => $projectId]);
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
}
