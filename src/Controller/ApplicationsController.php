<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Application;
use App\Model\Entity\FundingCycle;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenTime;
use Cake\Routing\Router;
use Cake\Utility\Security;

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
     * @param \App\Model\Entity\FundingCycle $fundingCycle
     */
    private function setFromNow(FundingCycle $fundingCycle)
    {
        // Set times to 00:00 to make "days from now" math easier
        $deadline = $fundingCycle->application_end->setTime(0, 0, 0, 0);
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
        $this->title('Apply for Funding');

        // Set data needed by form
        /** @var FundingCycle $fundingCycle */
        $fundingCycle = $this->FundingCycles->find('current')->first();
        if (!$fundingCycle) {
            $this->viewBuilder()->setTemplate('no_funding_cycle');
        }

        $application = $this->Applications->newEmptyEntity();
        $this->set([
            'application' => $application,
            'categories' => $this->Categories->getOrdered(),
            'fundingCycle' => $fundingCycle,
        ]);
        $this->setFromNow($fundingCycle);

        if (!$this->request->is('post')) {
            return null;
        }

        if (is_null($fundingCycle)) {
            $url = Router::url([
                'prefix' => false,
                'controller' => 'FundingCycles',
                'action'     => 'index',
            ]);
            $this->Flash->error(
                'Sorry, but applications are not being accepted at the moment. ' .
                "Please check back later, or visit the <a href=\"$url\">Funding Cycles</a> page for information " .
                'about upcoming application periods.'
            );
            return null;
        }

        // Process form
        $submitting = !isset($data['save']);
        $data = $this->request->getData();
        $application = $this->Applications->newEntity($data);
        $user = $this->request->getAttribute('identity');
        $application->user_id = $user ? $user->id : null;
        $application->funding_cycle_id = $fundingCycle->id;
        $application->status_id = $submitting ? Application::STATUS_UNDER_REVIEW : Application::STATUS_DRAFT;
        $verb = $submitting ? 'submitted' : 'saved';
        $hasErrors = false;
        if ($this->Applications->save($application)) {
            $this->Flash->success("The application has been $verb.");
        } else {
            $this->Flash->error("The application could not be $verb.");
            $hasErrors = true;
        }

        // Process image
        /** @var \Laminas\Diactoros\UploadedFile $rawImage */
        $rawImage = $data['image'];
        if ($rawImage && $rawImage->getSize() !== 0) {
            /** @var \App\Model\Entity\Image $image */
            $image = $this->Images->newEmptyEntity();
            $image->application_id = $application->id;
            $image->weight = 0;
            $image->caption = $data['imageCaption'];
            $filenameSplit = explode('.', $rawImage->getClientFilename());
            $image->filename = sprintf(
                '%s-%s.%s',
                $application->id,
                Security::randomString(10),
                end($filenameSplit)
            );
            $path = WWW_ROOT . 'img' . DS . 'applications' . DS . $image->filename;
            try {
                $rawImage->moveTo($path);
            } catch (\Exception $e) {
                $this->Flash->error(
                    'Unfortunately, there was an error uploading that image. Details: ' . $e->getMessage()
                );
                return null;
            }

            if (!$this->Images->save($image)) {
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
        $id = $this->request->getParam('id');
        /** @var \App\Model\Entity\User $user */
        $user = $this->Authentication->getIdentity();
        if (!$this->Applications->exists(['id' => $id, 'user_id' => $user])) {
            $this->Flash->error('Sorry, but that application is not available to view');
            return $this->redirect('/');
        }

        return $this->_view();
    }

    /**
     * @return \Cake\Http\Response|null
     */
    private function _view()
    {
        $id = $this->request->getParam('id');
        /** @var \App\Model\Entity\Application $application */
        $application = $this->Applications->find()->where(['id' => $id])->first();
        if (!$application) {
            $this->Flash->error('Sorry, but that application was not found');

            return $this->redirect('/');
        }

        $category = $this->Categories->find()->all()->toArray();
        $image = $this->Images->find()->where(['application_id' => $application->id])->first();
        $title = $application->title;
        $this->set(compact(
            'application',
            'category',
            'image',
            'title',
        ));

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
     * Page for resubmitting a returned application
     *
     * @return void
     */
    public function resubmit()
    {
        $id = $this->request->getParam('id');
        $application = $this->Applications->find()->where(['id' => $id])->first();
        if ($this->request->is('post')) {
            $application = $this->Applications->patchEntity($application, ['status_id' => Application::STATUS_UNDER_REVIEW]);
            if ($this->Applications->save($application)) {
                $this->Flash->success('Application has been resubmitted.');
            }
        }
        $this->title('Resubmit');
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
