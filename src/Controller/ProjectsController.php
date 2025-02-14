<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Project;
use App\Model\Entity\FundingCycle;
use App\Model\Entity\Image;
use App\Model\Entity\Transaction;
use Cake\Database\Expression\QueryExpression;
use Cake\Database\Query;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Security;
use Exception;

/**
 * ProjectsController
 *
 * @property \App\Model\Table\ProjectsTable $Projects
 * @property \App\Model\Table\CategoriesTable $Categories
 * @property \App\Model\Table\FundingCyclesTable $FundingCycles
 * @property \App\Model\Table\ImagesTable $Images
 */
class ProjectsController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->FundingCycles = $this->fetchTable('FundingCycles');
        $this->Categories = $this->fetchTable('Categories');
        $this->Images = $this->fetchTable('Images');

        $this->Authentication->allowUnauthenticated([
            'apply',
            'index',
            'view',
        ]);
    }

    /**
     * Sets the $fromNow viewVar
     *
     * @param FrozenTime $deadline
     */
    protected function setFromNow($deadline)
    {
        // Set times to 00:00 to make "days from now" math easier
        $deadline = $deadline->setTime(0, 0, 0, 0);
        $tz = \App\Application::LOCAL_TIMEZONE;
        $today = (FrozenTime::now($tz))->setTime(0, 0, 0, 0);
        $days = $deadline->diffInDays($today);
        switch ($days) {
            case 0:
                $fromNow = 'today';
                break;
            case 1:
                $fromNow = 'tomorrow';
                break;
            default:
                $fromNow = "$days days from now";
                break;
        }

        $this->set(['fromNow' => $fromNow]);
    }

    /**
     * Page for submitting an application
     *
     * @return \Cake\Http\Response|null
     */
    public function apply(): ?Response
    {
        $this->title('Apply for Funding');

        // Check if applications can be accepted
        /** @var FundingCycle $fundingCycle */
        $fundingCycle = $this->FundingCycles->find('current')->first();
        if (is_null($fundingCycle)) {
            $nextFundingCycle = $this->FundingCycles->find('nextApplying')->first();
            $this->set(compact('nextFundingCycle'));
            $this->viewBuilder()->setTemplate('applications_not_being_accepted');

            return null;
        }

        // Show nonstandard error message and redirect if unauthenticated
        $identity = $this->Authentication->getIdentity();
        if (!$identity) {
            $this->Flash->error('You\'ll need to register an account or log in before applying.');
            return $this->redirectToLogin();
        }

        // Set up view vars
        $this->viewBuilder()->setTemplate('form');
        $this->setProjectVars();
        $this->setFromNow($fundingCycle->application_end_local);
        $this->set('toLoad', $this->getAppFiles('image-uploader'));

        $user = $this->getAuthUser();

        // Process form
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $project = $this->Projects->newEntity($data, ['associated' => ['Answers']]);
            $project->user_id = $user->id;
            $project->funding_cycle_id = $fundingCycle->id;
            if ($this->processProject($project, $data)) {
                return $this->redirect([
                    'prefix' => 'My',
                    'controller' => 'Projects',
                    'action' => 'index'
                ]);
            }
        } else {
            /** @var Project $project */
            $project = $this->Projects->newEmptyEntity();
            $project->address = $user->address;
            $project->zipcode = $user->zipcode;
            $project->check_name = $user->name;
        }

        $this->set(compact('project'));

        return null;
    }

    /**
     * @param array $data
     * @return bool
     */
    private function processAddressUpdate(array $data): bool
    {
        $user = $this->getAuthUser();
        $usersTable = TableRegistry::getTableLocator()->get('Users');

        $usersTable->patchEntity($user, $data);
        if (!$usersTable->save($user)) {
            $this->Flash->error(
                'There was an error saving your address. Please correct any errors, try again, '
                . 'and <a href="/contact">contact us</a> if you need assistance.',
                ['escape' => false]
            );
            return false;
        }
        $this->Authentication->setIdentity($user);

        return true;
    }

    /**
     * @param Project $project
     * @param array $data
     * @return bool
     */
    protected function processProject($project, $data): bool
    {
        if (!$this->validateAgreements()) {
            return false;
        }

        $addressData = ['address' => $data['address'], 'zipcode' => $data['zipcode']];
        if (!$this->processAddressUpdate($addressData)) {
            return false;
        }

        if ($project->id) {
            $data = $this->applyProjectIdToAnswers($data, $project->id);
        }

        $submittingForReview = ($data['save-mode'] ?? null) == 'submit';
        $project->status_id = $submittingForReview ? Project::STATUS_UNDER_REVIEW : Project::STATUS_DRAFT;
        $verb = $submittingForReview ? 'submitted' : 'saved';
        $hasErrors = false;
        $project = $this->Projects->patchEntity($project, $data, ['associated' => ['Answers']]);
        if ($this->Projects->save($project)) {
            $this->Flash->success("Your application has been $verb.");
        } else {
            $this->Flash->error(
                "Your application could not be $verb. " . $this->errorTryAgainContactMsg,
                ['escape' => false]
            );
            $hasErrors = true;
        }

        $this->processImages($data, $project);

        return !$hasErrors;
    }

    /**
     * @param array $data Request data
     * @param Project $project
     * @return void
     */
    protected function processImages($data, $project): void
    {
        foreach ($data['images'] ?? [] as $key => $data) {
            $weight = $key + 1;
            $filename = $data['filename'] ?? null;
            $caption = $data['caption'] ?? '';

            // Find or create image
            $image = $this->Images->getByFilename($filename);
            if ($image) {
                // Validate to prevent the user from manipulating someone else's image
                if ($image->project_id != $project->id) {
                    $this->Flash->error(
                        "The image $filename is not associated with project {$project->id}"
                        . $this->errorTryAgainContactMsg,
                        ['escape' => false]
                    );
                    continue;
                }
            } else {
                /** @var Image $image */
                $image = $this->Images->newEmptyEntity();
                $image->project_id = $project->id;
                $image->filename = $filename;
            }

            // Set new weight and caption
            $image->weight = $weight;
            $image->caption = $caption;
            if (!$this->Images->save($image)) {
                $this->Flash->error(
                    'There was an error saving an image. Details: Record could not be added to database. '
                    . $this->errorTryAgainContactMsg,
                    ['escape' => false]
                );
            }
        }
    }

    /**
     * @param \Laminas\Diactoros\UploadedFile $rawImage
     * @param int $projectId
     * @param string $caption
     * @return \App\Model\Entity\Image|false|null
     */
    private function processImageUpload($rawImage, $projectId, $caption)
    {
        /** @var \App\Model\Entity\Image $image */
        $image = $this->Images->newEmptyEntity();
        $image->project_id = $projectId;
        $image->weight = 0;
        $image->caption = $caption;
        $filenameSplit = explode('.', $rawImage->getClientFilename());
        $image->filename = sprintf(
            '%s-%s.%s',
            $projectId,
            Security::randomString(10),
            end($filenameSplit)
        );
        $path = WWW_ROOT . 'img' . DS . 'projects' . DS . $image->filename;
        try {
            $rawImage->moveTo($path);
        } catch (Exception $e) {
            $this->Flash->error(
                'Unfortunately, there was an error uploading that image. Details: ' . $e->getMessage() . ' '
                . $this->errorTryAgainContactMsg,
                ['escape' => false]
            );
            return null;
        }

        return $this->Images->save($image);
    }

    /**
     * Page for viewing an arbitrary application
     *
     * @return \Cake\Http\Response|null
     */
    public function view(): ?Response
    {
        $id = $this->request->getParam('id');
        /** @var Project $project */
        $project = $this->Projects
            ->find('notDeleted')
            ->where(['id' => $id])
            ->contain(['Answers'])
            ->first();

        if (!$project) {
            $this->Flash->error('Project not found');
            $this->setResponse($this->getResponse()->withStatus(404));
            return $this->redirect('/');
        }

        if (!$project->isViewable()) {
            $this->Flash->error('Sorry, but that application is not available to view');
            return $this->redirect('/');
        }

        $this->addControllerBreadcrumb('Projects');
        $this->addBreadcrumb($project->title, []);

        return $this->_view();
    }

    /**
     * @return \Cake\Http\Response|null
     */
    protected function _view()
    {
        $projectId = $this->request->getParam('id');
        $exists = $this->Projects->exists([
            'Projects.id' => $projectId,
            'Projects.status_id != ' => Project::STATUS_DELETED
        ]);
        if (!$exists) {
            $this->Flash->error('Sorry, but that application was not found');
            return $this->redirect([
                'action' => 'index'
            ]);
        }

        $project = $this->Projects->getForViewing($projectId);
        $questionsTable = $this->fetchTable('Questions');
        $this->set([
            'project' => $project,
            'back' => $this->getRequest()->getQuery('back'),
            'questions' => $questionsTable->find('forProject')->toArray(),
        ]);
        $this->viewBuilder()->setTemplate('/Projects/view');
        $this->title('Project: ' . $project->title);

        $this->setCurrentBreadcrumb($project->title);

        return null;
    }

    /**
     * Sets view variables needed by the application form
     *
     * @return void
     */
    protected function setProjectVars()
    {
        /** @var FundingCycle $fundingCycle */
        $fundingCycle = $this->FundingCycles->find('current')->first();
        $categories = $this->Categories->getOrdered();
        $deadline = $fundingCycle?->application_end_local->format('F j, Y');
        $questionsTable = $this->fetchTable('Questions');
        $questions = $questionsTable->find('forProject')->toArray();
        $this->set(compact('categories', 'fundingCycle', 'deadline', 'questions'));
    }

    private function applyProjectIdToAnswers($data, $projectId): array
    {
        foreach ($data['answers'] as $i => $answer) {
            $data['answers'][$i]['project_id'] = $projectId;
        }
        return $data;
    }

    public function index()
    {
        $fundingCycles = $this->FundingCycles
            ->find()
            ->orderAsc('FundingCycles.application_end')
            ->contain([
                'Projects' => [
                    'queryBuilder' => function (Query $q) {
                        return $q
                            ->where(function (QueryExpression $exp) {
                                return $exp->in(
                                    'status_id',
                                    [
                                        Project::STATUS_ACCEPTED,
                                        Project::STATUS_AWARDED_NOT_YET_DISBURSED,
                                        Project::STATUS_AWARDED_AND_DISBURSED,
                                    ]
                                );
                            })
                            ->orderAsc('title');
                    },
                    'Transactions' => [
                        'queryBuilder' => function (Query $q) {
                            return $q
                                ->select([
                                    'Transactions.id',
                                    'Transactions.project_id',
                                    'Transactions.amount_gross',
                                    'Transactions.type',
                                ])
                                ->where(['Transactions.type' => Transaction::TYPE_LOAN]);
                        },
                    ],
                    'Categories',
                    'Images' => function (Query $q) {
                        return $q->orderAsc('weight');
                    },
                    'Users',
                    'FundingCycles',
                ]
            ])
            ->all();

        $this->addControllerBreadcrumb('Projects');
        $this->title('Projects');
        $this->set(compact('fundingCycles'));
    }

    /**
     * Ensures that all agreement checkboxes were submitted with truthy values
     *
     * @return bool
     */
    private function validateAgreements()
    {
        $agreements = [
            'loan-terms-agree',
            'eligibility-project-agree',
            'eligibility-applicant-agree',
        ];
        $data = $this->getRequest()->getData();
        foreach ($agreements as $agreement) {
            if (!(isset($data[$agreement]) && $data[$agreement])) {
                $this->Flash->error('You must agree to all terms in order to apply for funding.');
                return false;
            }
        }

        return true;
    }
}
