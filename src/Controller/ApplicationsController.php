<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Application;
use App\Model\Entity\FundingCycle;
use Cake\Event\EventInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Response;
use Cake\I18n\FrozenTime;
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
        $this->loadModel('FundingCycles');
        $this->loadModel('Categories');
        $this->loadModel('Images');
    }

    /**
     * Sets the $fromNow viewVar
     *
     * @param FrozenTime $deadline
     */
    private function setFromNow($deadline)
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
     * @throws \Cake\Http\Exception\ForbiddenException When a directory traversal attempt.
     * @throws \Cake\Http\Exception\NotFoundException When the view file could not
     *   be found or \Cake\View\Exception\MissingTemplateException in debug mode.
     */
    public function apply(): ?Response
    {
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

        $this->title('Apply for Funding');
        $this->viewBuilder()->setTemplate('form');
        $application = $this->Applications->newEmptyEntity();
        $categories = $this->Categories->getOrdered();
        $deadline = $fundingCycle->application_end->format('F j, Y');
        $this->set(compact('application', 'categories', 'fundingCycle', 'deadline'));
        $this->setFromNow($fundingCycle->application_end);

        if (!$this->request->is('post')) {
            return null;
        }

        // Process form
        $data = $this->request->getData();
        $application = $this->Applications->newEntity($data);
        $user = $this->request->getAttribute('identity');
        $application->user_id = $user ? $user->id : null;
        $application->funding_cycle_id = $fundingCycle->id;
        $savingToDraft = isset($data['save']);
        $application->status_id = $savingToDraft ? Application::STATUS_DRAFT : Application::STATUS_UNDER_REVIEW;
        $verb = $savingToDraft ? 'saved' : 'submitted';
        $hasErrors = false;
        if ($this->Applications->save($application)) {
            $this->Flash->success("Your application has been $verb.");
        } else {
            $this->Flash->error("Your application could not be $verb.");
            $hasErrors = true;
        }

        // Process image
        /** @var \Laminas\Diactoros\UploadedFile $rawImage */
        $rawImage = $data['image'];
        if ($rawImage && $rawImage->getSize() !== 0) {
            if (!$this->processImageUpload($rawImage, $application->id, $data['imageCaption'])) {
                $hasErrors = true;
            }
        }

        // Bounce back to form on error
        if ($hasErrors) {
            return null;
        }

        // Otherwise, go to applications index page
        return $this->redirect(['index']);
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
        $application = $this->Applications->find()->where(['id' => $id])->first();

        // Statuses in which an application is viewable by the public
        $viewableStatuses = [
            Application::STATUS_VOTING,
            Application::STATUS_AWARDED,
            Application::STATUS_NOT_AWARDED,
        ];

        $isViewable = in_array($application->status_id, $viewableStatuses);
        if (!$isViewable) {
            $this->Flash->error('Sorry, but that application is not available to view');
            return $this->redirect('/');
        }

        return $this->_view();
    }

    /**
     * Page for viewing one's own application
     *
     * @return \Cake\Http\Response|null
     */
    public function viewMy(): ?Response
    {
        $applicationId = $this->request->getParam('id');
        if (!$this->isOwnApplication($applicationId)) {
            $this->Flash->error('Sorry, but that application is not available to view');
            return $this->redirect('/');
        }

        return $this->_view();
    }

    /**
     * Returns TRUE if the current user owns the specified application
     *
     * @param int $applicationId
     * @return bool
     */
    private function isOwnApplication($applicationId): bool
    {
        /** @var \App\Model\Entity\User $user */
        $user = $this->Authentication->getIdentity();

        return $this->Applications->exists(['id' => $applicationId, 'user_id' => $user->id]);
    }

    /**
     * @return \Cake\Http\Response|null
     */
    private function _view()
    {
        $applicationId = $this->request->getParam('id');
        /** @var \App\Model\Entity\Application $application */
        $application = $this->Applications
            ->find()
            ->where(['Applications.id' => $applicationId])
            ->contain(['Images', 'Categories', 'FundingCycles'])
            ->first();
        if (!$application) {
            $this->Flash->error('Sorry, but that application was not found');

            return $this->redirect('/');
        }

        $this->set(compact('application'));
        $this->title('Application: ' . $application->title);
        $this->viewBuilder()->setTemplate('view');

        return null;
    }

    /**
     * Page for withdrawing an application from consideration
     *
     * @return void
     */
    public function withdraw()
    {
        $id = $this->request->getParam('id');
        $application = $this->Applications->find()->where(['id' => $id])->first();
        if ($this->request->is('post')) {
            $application = $this->Applications->patchEntity($application, ['status_id' => Application::STATUS_WITHDRAWN]);
            if ($this->Applications->save($application)) {
                $this->Flash->success('Application withdrawn.');
            }
        }
        $this->set(['title' => 'Withdraw']);
    }

    /**
     * Page for updating a draft or (re)submitting an application
     *
     * @return \Cake\Http\Response|null
     */
    public function edit(): ?Response
    {
        $applicationId = $this->request->getParam('id');
        if (!$this->isOwnApplication($applicationId)) {
            $this->Flash->error('That application was not found');
            return $this->redirect('/');
        }

        /** @var Application $application */
        $application = $this->Applications
            ->find()
            ->where(['id' => $applicationId])
            ->contain('FundingCycles')
            ->first();

        $isUpdatable = in_array(
            $application->status_id,
            [Application::STATUS_DRAFT, Application::STATUS_REVISION_REQUESTED]
        );
        if (!$isUpdatable) {
            $this->Flash->error('That application cannot currently be updated.');
            return $this->redirect('/');
        }

        $this->title('Resubmit');
        $this->viewBuilder()->setTemplate('form');
        // If application is draft, deadline is application_end
        // If application is revision-requested, deadline is resubmit_deadline
        switch ($application->status_id) {
            case Application::STATUS_DRAFT:
                $deadline = $application->funding_cycle->application_end;
                break;
            case Application::STATUS_REVISION_REQUESTED:
                $deadline = $application->funding_cycle->resubmit_deadline;
                break;
            default:
                throw new BadRequestException('That application cannot currently be updated.');
        }
        $this->set(compact('application'));
        $this->setFromNow($deadline);

        if (!$this->request->is('post')) {
            return null;
        }

        $data = $this->request->getData();
        $application = $this->Applications->patchEntity($application, $data);
        $savingToDraft = isset($data['save']);

        // If saving, status doesn't change. Otherwise, it's submitted for review.
        if (!$savingToDraft) {
            $application->status_id = Application::STATUS_UNDER_REVIEW;
        }

        if ($this->Applications->save($application)) {
            $verb = $savingToDraft ? 'updated' : 'submitted';
            $this->Flash->success("Application has been $verb.");
        }

        return null;
    }

    /**
     * Page for removing an application
     *
     * @return void
     */
    public function delete()
    {
        $id = $this->request->getParam('id');
        $application = $this->Applications->find()->where(['id' => $id])->first();
        if ($this->request->is('delete')) {
            if ($this->Applications->delete($application)) {
                $this->Flash->success('Application has been deleted');
            }
        }
        $this->title('Delete');
    }

    public function index()
    {
        $this->title('My Applications');
        /** @var \App\Model\Entity\User $user */
        $user = $this->Authentication->getIdentity();
        $applications = $this->Applications
            ->find()
            ->where(['user_id' => $user->id])
            ->orderDesc('Applications.created')
            ->contain(['FundingCycles'])
            ->all();
        $this->set(compact('applications'));
    }
}
