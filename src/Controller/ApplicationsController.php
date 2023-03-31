<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Application;
use App\Model\Entity\FundingCycle;
use App\Model\Entity\Image;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Security;
use Exception;

/**
 * ApplicationsController
 *
 * @property \App\Model\Table\ApplicationsTable $Applications
 * @property \App\Model\Table\CategoriesTable $Categories
 * @property \App\Model\Table\FundingCyclesTable $FundingCycles
 * @property \App\Model\Table\ImagesTable $Images
 */
class ApplicationsController extends AppController
{
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->FundingCycles = $this->fetchTable('FundingCycles');
        $this->Categories = $this->fetchTable('Categories');
        $this->Images = $this->fetchTable('Images');

        $this->Authentication->allowUnauthenticated(['apply']);
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
        // Check if applications can be accepted
        /** @var FundingCycle $fundingCycle */
        $fundingCycle = $this->FundingCycles->find('current')->first();
        if (is_null($fundingCycle)) {
            $url = Router::url([
                'prefix' => false,
                'controller' => 'FundingCycles',
                'action'     => 'index',
            ]);
            $this->Flash->error(
                'Sorry, but applications are not being accepted at the moment. ' .
                "Please check back later, or visit the <a href=\"$url\">Funding Cycles</a> page for information " .
                'about upcoming application periods.',
                ['escape' => false]
            );
            return $this->redirect('/');
        }

        // Show nonstandard error message and redirect if unauthenticated
        $identity = $this->Authentication->getIdentity();
        if (!$identity) {
            $this->Flash->error('You\'ll need to register an account or log in before applying.');
            return $this->redirect(['controller' => 'Users', 'action' => 'register']);
        }

        // Set up view vars
        $this->title('Apply for Funding');
        $this->viewBuilder()->setTemplate('form');
        $this->setApplicationVars();
        $this->setFromNow($fundingCycle->application_end);

        $userId = $identity->getIdentifier();
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $user = $usersTable->get($userId);

        // Process form
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $application = $this->Applications->newEntity($data, ['associated' => ['Answers']]);
            $application->user_id = $userId;
            $application->funding_cycle_id = $fundingCycle->id;
            if ($this->processApplication($application, $data)) {
                return $this->redirect(['action' => 'index']);
            }
        } else {
            /** @var Application $application */
            $application = $this->Applications->newEmptyEntity();
            $application->address = $user->address;
            $application->zipcode = $user->zipcode;
            $application->check_name = $user->name;
        }

        $this->set(compact('application'));

        return null;
    }

    /**
     * @param array $data
     * @return bool
     */
    private function processAddressUpdate(array $data): bool
    {
        $identity = $this->Authentication->getIdentity();
        $userId = $identity->getIdentifier();
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $user = $usersTable->get($userId);
        $usersTable->patchEntity($user, $data);
        if (!$usersTable->save($user)) {
            $this->Flash->error(
                'There was an error saving your address. Please correct any errors, try again, ' .
                'and contact us if you need assistance.'
            );
            return false;
        }

        return true;
    }

    /**
     * @param Application $application
     * @param array $data
     * @return bool
     */
    protected function processApplication($application, $data): bool
    {
        $addressData = ['address' => $data['address'], 'zipcode' => $data['zipcode']];
        if (!$this->processAddressUpdate($addressData)) {
            return false;
        }

        if ($application->id) {
            $data = $this->applyApplicationIdToAnswers($data, $application->id);
        }
        $savingToDraft = isset($data['save']);
        $application->status_id = $savingToDraft ? Application::STATUS_DRAFT : Application::STATUS_UNDER_REVIEW;
        $verb = $savingToDraft ? 'saved' : 'submitted';
        $hasErrors = false;
        $application = $this->Applications->patchEntity($application, $data, ['associated' => ['Answers']]);
        if ($this->Applications->save($application)) {
            $this->Flash->success("Your application has been $verb.");
        } else {
            $this->Flash->error("Your application could not be $verb.");
            $hasErrors = true;
        }

        // Process new images
        foreach ($data['filepond'] ?? [] as $imageEncoded) {
            if (!is_string($imageEncoded)) {
                continue;
            }

            /** @var \stdClass $imageFilenames */
            $imageFilenames = json_decode($imageEncoded);
            if (!($imageFilenames->full ?? false) || !($imageFilenames->thumb ?? false)) {
                continue;
            }

            /** @var Image $image */
            $image = $this->Images->newEmptyEntity();
            $image->application_id = $application->id;
            $image->weight = 0;
            $image->filename = $imageFilenames->full;
            if (!$this->Images->save($image)) {
                $this->Flash->error(
                    'There was an error saving an image. Details: Record could not be added to database'
                );
            }
        }

        // Delete images
        foreach ($data['delete-image'] ?? [] as $imageId) {
            if (!$this->Images->exists(['id' => $imageId])) {
                continue;
            }
            $image = $this->Images->get($imageId);
            $thumbFilename = Image::THUMB_PREFIX . $image->filename;
            $path = WWW_ROOT . 'img' . DS . 'applications' . DS;
            if (file_exists($path . $image->filename)) {
                unlink($path . $image->filename);
            }
            if (file_exists($path . $thumbFilename)) {
                unlink($path . $thumbFilename);
            }
            $this->Images->delete($image);
        }

        return !$hasErrors;
    }

    /**
     * @param \Laminas\Diactoros\UploadedFile $rawImage
     * @param int $applicationId
     * @param string $caption
     * @return \App\Model\Entity\Image|false|null
     */
    private function processImageUpload($rawImage, $applicationId, $caption)
    {
        /** @var \App\Model\Entity\Image $image */
        $image = $this->Images->newEmptyEntity();
        $image->application_id = $applicationId;
        $image->weight = 0;
        $image->caption = $caption;
        $filenameSplit = explode('.', $rawImage->getClientFilename());
        $image->filename = sprintf(
            '%s-%s.%s',
            $applicationId,
            Security::randomString(10),
            end($filenameSplit)
        );
        $path = WWW_ROOT . 'img' . DS . 'applications' . DS . $image->filename;
        try {
            $rawImage->moveTo($path);
        } catch (Exception $e) {
            $this->Flash->error(
                'Unfortunately, there was an error uploading that image. Details: ' . $e->getMessage()
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
        /** @var Application $application */
        $application = $this->Applications
            ->find()
            ->where(['id' => $id])
            ->contain(['Answers'])
            ->first();

        if (!$application->isViewable()) {
            $this->Flash->error('Sorry, but that application is not available to view');
            return $this->redirect('/');
        }

        return $this->_view();
    }

    /**
     * @return \Cake\Http\Response|null
     */
    protected function _view()
    {
        $applicationId = $this->request->getParam('id');
        if (!$this->Applications->exists(['Applications.id' => $applicationId])) {
            $this->Flash->error('Sorry, but that application was not found');
            return $this->redirect('/');
        }

        $application = $this->Applications->getForViewing($applicationId);
        $questionsTable = $this->fetchTable('Questions');
        $this->set([
            'application' => $application,
            'back' => $this->getRequest()->getQuery('back'),
            'questions' => $questionsTable->find('forApplication')->toArray(),
        ]);
        $this->viewBuilder()->setTemplate('/Applications/view');
        $this->title('Application: ' . $application->title);

        $this->setCurrentBreadcrumb($application->title);

        return null;
    }

    /**
     * Sets view variables needed by the application form
     *
     * @return void
     */
    protected function setApplicationVars()
    {
        /** @var FundingCycle $fundingCycle */
        $fundingCycle = $this->FundingCycles->find('current')->first();
        $categories = $this->Categories->getOrdered();
        $deadline = $fundingCycle?->application_end->format('F j, Y');
        $questionsTable = $this->fetchTable('Questions');
        $questions = $questionsTable->find('forApplication')->toArray();
        $this->set(compact('categories', 'fundingCycle', 'deadline', 'questions'));
    }

    private function applyApplicationIdToAnswers($data, $applicationId): array
    {
        foreach ($data['answers'] as $i => $answer) {
            $data['answers'][$i]['application_id'] = $applicationId;
        }
        return $data;
    }
}
