<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;

/**
 * FundingCyclesController
 *
 * @property \App\Model\Table\ApplicationsTable $Applications
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
    }

    /**
     * Page for reviewing an application
     *
     * @return void
     */
    public function review()
    {
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
