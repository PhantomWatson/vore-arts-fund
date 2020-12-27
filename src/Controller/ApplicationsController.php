<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

use Cake\Http\Response;
use Cake\ORM\TableRegistry;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 * @property \App\Model\Table\ApplicationsTable $Applications
 */
class ApplicationsController extends AppController
{
    /**
     * Displays a view
     *
     * @return \Cake\Http\Response|null
     * @throws \Cake\Http\Exception\ForbiddenException When a directory traversal attempt.
     * @throws \Cake\Http\Exception\NotFoundException When the view file could not
     *   be found or \Cake\View\Exception\MissingTemplateException in debug mode.
     */
    public function apply(): ?Response
    {
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $applicationsTable = TableRegistry::getTableLocator()->get('applications');
            $imagesTable = TableRegistry::getTableLocator()->get('images');
            $fundingCyclesTable = TableRegistry::getTableLocator()->get('funding_cycles');
            $fundingCycle = $fundingCyclesTable->find('all', ['conditions' => ['funding_cycles.application_begin <=' => date('Y-m-d H:i:s'), 'funding_cycles.application_end >=' => date('Y-m-d H:i:s')], 'fields' => ['funding_cycles.id']])->first();
            if (!is_null($fundingCycle)) {
                $application = $applicationsTable->newEntity($data);
                $application->category_id = $data['category'] + 1;
                $application->user_id = $this->Auth->user('id');
                $application->funding_cycle_id = $fundingCycle->id;
                $application->status_id = isset($data['save']) ? 1 : 0;
                $result = $applicationsTable->save($application);
                if ($result) {
                    $this->Flash->success(__('The application has been ' . (isset($data['save']) ? 'saved.' : 'submitted.')));
                } else {
                    $this->Flash->error(__('The application could not be ' . (isset($data['save']) ? 'saved.' : 'submitted.')));
                }
                $rawImage = $data['image'];
                if ($rawImage['size'] !== 0) {
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
        }

        return null;
    }

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
