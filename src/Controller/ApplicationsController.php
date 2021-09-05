<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Http\Response;

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
            $this->Flash->error(__('No valid funding cycle.'));
            return null;
        }

        // Process form
        $data = $this->request->getData();
        $application = $this->Applications->newEntity($data);
        $application->user_id = $this->Auth->user('id');
        $application->funding_cycle_id = $fundingCycle->id;
        $application->status_id = isset($data['save']) ? 1 : 0;
        $result = $this->Applications->save($application);
        if ($result) {
            $this->Flash->success(
                'The application has been ' . (isset($data['save']) ? 'saved.' : 'submitted.')
            );
        } else {
            $this->Flash->error(
                'The application could not be ' . (isset($data['save']) ? 'saved.' : 'submitted.')
            );
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
            if (move_uploaded_file($rawImage['tmp_name'], WWW_ROOT . $path) && $this->Images->save($image)) {
                $this->Flash->success(__('The image has been saved.'));
            } else {
                $this->Flash->error(__('The image could not be saved.'));
            }
        }

        return null;
    }

    /**
     * Page for viewing an application
     *
     * @return void
     */
    public function view()
    {
        $id = $this->request->getParam('id');
        $application = null;
        if ($this->request->is('get')) {
            $application = $this->Applications->find()->where(['id' => $id])->first()->toArray();
        }
        $this->set(compact('application'));
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
            $application = $this->Applications->patchEntity($application, ['status_id' => 8]);
            if ($this->Applications->save($application)) {
                $this->Flash->success('Application withdrawn.');
            }
        }
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
            $application = $this->Applications->patchEntity($application, ['status_id' => 9]);
            if ($this->Applications->save($application)) {
                $this->Flash->success('Application has been resubmitted.');
            }
        }
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
    }
}
