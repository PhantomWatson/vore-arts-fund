<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;

/**
 * FundingCyclesController
 *
 * @property \App\Model\Table\ApplicationsTable $Applications
 * @property \App\Model\Table\CategoriesTable $Categories
 * @property \App\Model\Table\ImagesTable $Images
 * @property \App\Model\Table\StatusesTable $Statuses
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */

class ApplicationsController extends AppController
{
    /**
     * Applications index page
     *
     * @return void
     */
    public function index()
    {
        $this->title('Applications');
    }

    /**
     * Page for reviewing an application
     *
     * @return void
     */
    public function review()
    {
        $this->loadModel('Applications');
        $this->loadModel('Categories');
        $this->loadModel('Images');
        $this->loadModel('Statuses');

        $application = $this->Applications->get($this->request->getParam('id'));
        $category = $this->Categories->find()->all()->toArray();
        $image = $this->Images->find()->where(['application_id' => $application['id']])->first();
        $statuses = $this->Statuses->find()->all();
        $statusOptions = [];
        foreach ($statuses as $status) {
            $statusOptions[$status->id] = $status->name;
        }
        $title = $application['title'];
        $this->set(compact(
            'application',
            'category',
            'image',
            'statuses',
            'statusOptions',
            'title',
        ));
    }

    /**
     * Page for changing the status of an application
     *
     * @return void
     */
    public function setStatus()
    {
        $id = $this->request->getParam('id');

        if ($this->request->is('post')) {
            $application = $this->Applications->get($id);
            $application = $this->Applications->patchEntity($application, $this->request->getData());
            if ($this->Applications->save($application)) {
                $this->Flash->success(__('Successfully updated application status'));
            } else {
                $this->Flash->error(__('Error updating application status'));
            }
        }
    }
}
