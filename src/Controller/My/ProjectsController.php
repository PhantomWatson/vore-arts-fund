<?php
declare(strict_types=1);

namespace App\Controller\My;

use App\Controller\ProjectsController as BaseProjectsController;
use App\Model\Entity\Note;
use App\Model\Entity\Project;
use App\Model\Table\NotesTable;
use Cake\Database\Expression\QueryExpression;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;

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
        $project = $this->Projects->get($id);
        $project->status_id = Project::STATUS_WITHDRAWN;
        if ($this->Projects->save($project)) {
            $this->Flash->success('Application withdrawn.');
        } else {
            $this->Flash->error('There was an error withdrawing your application.');
        }
        return $this->redirect(['action' => 'index']);
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
        $this->set('toLoad', $this->getAppFiles('image-uploader'));

        // Process form
        if ($this->request->is('put')) {
            $data = $this->request->getData();

            // If saving, status doesn't change. Otherwise, it's submitted for review.
            $savingToDraft = isset($data['save']);
            if (!$savingToDraft) {
                $project->status_id = Project::STATUS_UNDER_REVIEW;
            }

            if ($this->processProject($project, $data)) {
                return $this->redirect(['action' => 'index']);
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
     * Page for removing an application
     *
     * @return Response
     */
    public function delete()
    {
        $id = $this->request->getParam('id');
        $project = $this->Projects->get($id);
        if ($this->request->is(['delete', 'post']) && $this->Projects->delete($project)) {
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
            ->find()
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

        /** @var Project $project */
        $project = $this->Projects->get($projectId);

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
        $project = $this->Projects->get($projectId, ['contain' => 'Users']);

        if (!$project->isAwarded()) {
            $this->Flash->error('This project is not marked as having been awarded a loan.');
            $this->setResponse($this->getResponse()->withStatus(404));
            return $this->redirect([
                'prefix' => 'My',
                'controller' => 'Projects',
                'action' => 'index',
            ]);
        }

        $this->title('Loan Agreement');
        $this->addBreadcrumb($project->title, []);

        if ($project->loan_agreement_date) {
            // Show signed agreement, using the version signed
        } else {
            $this->newLoanAgreement($project);
        }
    }

    /**
     * @param Project $project
     * @return void
     */
    public function newLoanAgreement($project)
    {
        $setupComplete = false;
        $version = Project::getLatestTermsVersion();

        // Confirm loan recipient info
        if ($this->getRequest()->getData('setup')) {
            $setupComplete = $this->newLoanAgreementSetup($project);

        // Agree to terms and enter TIN
        } elseif ($this->getRequest()->getData('agreement')) {
            $data = [
                'tin' => $this->request->getData('tin'),
                'loan_agreement_date' => new \DateTime(),
                'loan_due_date' => new \DateTime(\App\Model\Entity\Project::DUE_DATE),
                'loan_agreement_version' => $version
            ];
            $this->Projects->patchEntity($project, $data);
            if ($this->Projects->save($project)) {
                $setupComplete = true;
            }
        }

        $this->set(compact('project'));

        $this->viewBuilder()->setTemplate(
            $setupComplete
                ? 'loan_agreement'
                : 'loan_agreement_setup'
        );
    }

    /**
     * @param Project $project
     * @return bool
     */
    public function newLoanAgreementSetup($project)
    {
        $this->Projects->patchEntity(
            $project,
            $this->request->getData(),
            ['fields' => ['check_name', 'address', 'zipcode']]
        );
        if ($this->Projects->save($project)) {
            return true;
        }

        return false;
    }
}
