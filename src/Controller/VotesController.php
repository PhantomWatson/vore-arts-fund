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

use Cake\ORM\TableRegistry;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 * @property \App\Model\Table\VotesTable $Votes
 */
class VotesController extends AppController
{
    /**
     * Displays a view
     *
     * @param array ...$event Path segments.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Http\Exception\ForbiddenException When a directory traversal attempt.
     * @throws \Cake\Http\Exception\NotFoundException When the view file could not
     *   be found or \Cake\View\Exception\MissingTemplateException in debug mode.
     */

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        $this->Auth->allow(['index', 'view']);
    }

    public function index(...$path)
    {
        $applications = TableRegistry::getTableLocator()->get('Applications')->find()->where(['status_id' => 5 ])->all()->toArray();
        $this->set(['applications' => $applications]);
    }

    public function submit()
    {
        $fundingCyclesTable = TableRegistry::getTableLocator()->get('funding_cycles');
        $fundingCycle = $fundingCyclesTable->find('all', ['conditions' => ['funding_cycles.application_begin <=' => date('Y-m-d H:i:s'), 'funding_cycles.application_end >=' => date('Y-m-d H:i:s')], 'fields' => ['funding_cycles.id']])->first();
        $voteTable = TableRegistry::getTableLocator()->get('votes');

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $keys = array_keys($data);

            foreach ($keys as $key) {
                $voteEntry = $voteTable->newEntity();
                $voteEntry->user_id = $this->Auth->user('id');
                $voteEntry->application_id = $key;
                $voteEntry->funding_cycle_id = $fundingCycle->id;
                $voteEntry->weight = 1;
                $result = $voteTable->save($voteEntry);
            }
            if ($result) {
                $this->Flash->success(__('Your votes have successfully been submitted.'));

                return $this->redirect('/');
            } else {
                $this->Flash->error(__('Your votes could not be submitted.'));

                return $this->redirect('/vote');
            }
        }
    }
}
