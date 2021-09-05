<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Response;
use Cake\ORM\TableRegistry;

/**
 * ApplicationsController
 *
 * @property \App\Model\Table\ApplicationsTable $Applications
 * @property \App\Model\Table\CategoriesTable $Categories
 */
class ApplicationsController extends AppController
{
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
        $this->loadModel('Categories');
        $application = $this->Applications->newEmptyEntity();
        $this->set([
            'application' => $application,
            'categories' => $this->Categories->getOrdered(),
        ]);
        if (!$this->request->is('post')) {
            return null;
        }

        $data = $this->request->getData();
        $applicationsTable = TableRegistry::getTableLocator()->get('applications');
        $imagesTable = TableRegistry::getTableLocator()->get('images');
        $fundingCyclesTable = TableRegistry::getTableLocator()->get('funding_cycles');
        $now = date('Y-m-d H:i:s');
        $fundingCycle = $fundingCyclesTable
            ->find()
            ->select(['FundingCycles.id'])
            ->where([
                'FundingCycles.application_begin <=' => $now,
                'FundingCycles.application_end >=' => $now,
            ])
            ->first();
        if (!is_null($fundingCycle)) {
            /** @var \App\Model\Entity\Application $application */
            $application = $applicationsTable->newEntity($data);
            $application->user_id = $this->Auth->user('id');
            $application->funding_cycle_id = $fundingCycle->id;
            $application->status_id = isset($data['save']) ? 1 : 0;
            $result = $applicationsTable->save($application);
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
                $image = $imagesTable->newEmptyEntity();
                $image->application_id = $result->id;
                $image->weight = 0;
                $path = DS . 'img' . DS . $rawImage['name'];
                $path = str_replace(' ', '', $path);
                $image->path = $path;
                $image->caption = $data['imageCaption'];
                if (move_uploaded_file($rawImage['tmp_name'], WWW_ROOT . $path) && $imagesTable->save($image)) {
                    $this->Flash->success(__('The image has been saved.'));
                } else {
                    $this->Flash->error(__('The image could not be saved.'));
                }
            }
        } else {
            $this->Flash->error(__('No valid funding cycle.'));
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
            $applicationsTable = TableRegistry::getTableLocator()->get('applications');
            $application = $applicationsTable->find()->where(['id' => $id])->first()->toArray();
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
        $applicationsTable = TableRegistry::getTableLocator()->get('applications');
        $application = $applicationsTable->find()->where(['id' => $id])->first();
        if ($this->request->is('post')) {
            $application = $applicationsTable->patchEntity($application, ['status_id' => 8]);
            if ($applicationsTable->save($application)) {
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
        $applicationsTable = TableRegistry::getTableLocator()->get('applications');
        $application = $applicationsTable->find()->where(['id' => $id])->first();
        if ($this->request->is('post')) {
            $application = $applicationsTable->patchEntity($application, ['status_id' => 9]);
            if ($applicationsTable->save($application)) {
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
        $applicationsTable = TableRegistry::getTableLocator()->get('applications');
        $application = $applicationsTable->find()->where(['id' => $id])->first();
        if ($this->request->is('delete')) {
            if ($applicationsTable->delete($application)) {
                $this->Flash->success('Application has been deleted');
            }
        }
    }
}
