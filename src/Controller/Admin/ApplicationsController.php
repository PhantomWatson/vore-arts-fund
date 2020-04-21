<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use App\Model\Table\ApplicationsTable;

/**
 * FundingCyclesController
 *
 * @property ApplicationsTable $Applications
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */

class ApplicationsController extends AppController
{

    public function index() {
        return null;
    }

    public function review() {
        return null;   
    }

    public function setStatus() {
        $id = $this->request->getParam('id');

        if ($this->request->is('post')) {
            $application = $this->Applications->get($id);
            $application = $this->Applications->patchEntity($application, $this->request->getData());
            if($this->Applications->save($application)){
                $this->Flash->success(__('Successfully updated application status'));
            } else {
                $this->Flash->error(__('Error updating application status'));
            }
        }
        return null;
    }

}
