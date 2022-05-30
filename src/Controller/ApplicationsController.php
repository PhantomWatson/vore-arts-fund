<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Application;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\Routing\Router;

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
        $data = $this->request->getData();
        $application = $this->Applications->newEntity($data);
        $user = $this->request->getAttribute('identity');
        $application->user_id = $user ? $user->id : null;
        $application->funding_cycle_id = $fundingCycle->id;
        $application->status_id = isset($data['save']) ? Application::STATUS_APPLYING : 0;
        $result = $this->Applications->save($application);
        $verb = isset($data['save']) ? 'saved' : 'submitted';
        if ($result) {
            $this->Flash->success("The application has been $verb.");
        } else {
            $this->Flash->error("The application could not be $verb.");
        }
        $rawImage = $data['image'];
        if ($rawImage['size'] !== 0) {
            /** @var \App\Model\Entity\Image $image */
            $image = $this->Images->newEmptyEntity();
            $image->application_id = $result->id;
            $image->weight = 0;
            $path = DS . 'img' . DS . $rawImage['name'];
            $path = str_replace(' ', '', $path);
            $image->path = $path;
            $image->caption = $data['imageCaption'];
            if (!move_uploaded_file($rawImage['tmp_name'], WWW_ROOT . $path) && $this->Images->save($image)) {
                $this->Flash->error('Unfortunately, there was an error uploading that image.');
            }
        }

        return null;
    }

    /**
     * Page for viewing an application
     *
     * @return \Cake\Http\Response|null
     */
    public function view()
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
}
