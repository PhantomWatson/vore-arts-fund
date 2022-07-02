<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Model\Entity\Application;

/**
 * FundingCyclesController
 *
 * @property \App\Model\Table\ApplicationsTable $Applications
 * @property \App\Model\Table\CategoriesTable $Categories
 * @property \App\Model\Table\ImagesTable $Images
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */

class ApplicationsController extends AdminController
{
    /**
     * Applications index page
     *
     * @return void
     */
    public function index($fundingCycleId = null)
    {
        $this->title('Applications');
        $fundingCyclesTable = $this->fetchTable('FundingCycles');
        $applications = [];

        if (!$fundingCycleId) {
            $currentCycle = $fundingCyclesTable->find('current')->first();
            $fundingCycleId = $currentCycle ? $currentCycle->id : null;
        }

        if ($fundingCycleId) {
            $applications = $this
                ->Applications
                ->find()
                ->where(['funding_cycle_id' => $fundingCycleId])
                ->all();
        }

        $this->set([
            'applications' => $applications,
            'fundingCycles' => $fundingCyclesTable->find()->orderDesc('application_begin')->all(),
            'fundingCycleId' => $fundingCycleId,
        ]);
    }

    /**
     * Page for reviewing an application
     *
     * @return void
     */
    public function review()
    {
        $applicationId = $this->request->getParam('id');
        $statusOptions = Application::getStatuses();
        $this->setViewApplicationViewVars($applicationId);
        $this->set(compact('statusOptions'));
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
