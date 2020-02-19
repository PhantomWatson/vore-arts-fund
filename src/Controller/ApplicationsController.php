<?php

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

use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
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
     * @param array ...$path Path segments.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Http\Exception\ForbiddenException When a directory traversal attempt.
     * @throws \Cake\Http\Exception\NotFoundException When the view file could not
     *   be found or \Cake\View\Exception\MissingTemplateException in debug mode.
     */
    public function apply(...$path)
    {
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $applicationsTable = TableRegistry::getTableLocator()->get('applications');
            $fundingCyclesTable = TableRegistry::getTableLocator()->get('funding_cycles');
            $fundingCycle = $fundingCyclesTable->find('all', ['conditions' => ['funding_cycles.application_begin <=' => date('Y-m-d H:i:s'), 'funding_cycles.application_end >=' => date('Y-m-d H:i:s')], 'fields' => ['funding_cycles.id']])->first();
            if (!is_null($fundingCycle)) {
                $application = $applicationsTable->newEntity($data);
                $application->category_id = $data['category'];
                $application->user_id = $this->Auth->user('id');
                $application->funding_cycle_id = $fundingCycle->id;
                $application->status_id = isset($data['save']) ? 1 : 0;
                if ($applicationsTable->save($application)) {
                    $this->Flash->success(__('The application has been ' . (isset($data['save']) ? 'saved.' : 'submitted.')));
                } else {
                    $this->Flash->error(__('The application could not be ' . (isset($data['save']) ? 'saved.' : 'submitted.')));
                }
            } else {
                $this->Flash->error(__('No valid funding cycle.'));
            }
        }
        return null;
    }
}
