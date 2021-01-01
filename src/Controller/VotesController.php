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

use Cake\Event\EventInterface;
use Cake\Http\Response;
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
     * @param \Cake\Event\EventInterface $event Event object
     * @return \Cake\Http\Response|void|null
     * @throws \Cake\Http\Exception\ForbiddenException When a directory traversal attempt.
     * @throws \Cake\Http\Exception\NotFoundException When the view file could not
     *   be found or \Cake\View\Exception\MissingTemplateException in debug mode.
     */
    public function beforeFilter(EventInterface $event): ?Response
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['index', 'view']);
    }

    /**
     * Votes index page
     *
     * @return void
     */
    public function index()
    {
        $applications = TableRegistry::getTableLocator()->get('Applications')
            ->find()
            ->where(['status_id' => 5 ])
            ->all()
            ->toArray();
        $this->set(['applications' => $applications]);
    }

    /**
     * @return \Cake\Http\Response|null
     */
    public function submit(): ?Response
    {
        $fundingCyclesTable = TableRegistry::getTableLocator()->get('funding_cycles');
        $now = date('Y-m-d H:i:s');
        $fundingCycle = $fundingCyclesTable
            ->find()
            ->where([
                'FundingCycles.application_begin <=' => $now,
                'FundingCycles.application_end >=' => $now,
            ])
            ->select(['FundingCycles.id'])
            ->first();
        $voteTable = TableRegistry::getTableLocator()->get('votes');

        if (!$this->request->is('post')) {
            return null;
        }

        $data = $this->request->getData();
        $keys = array_keys($data);

        $success = false;
        foreach ($keys as $key) {
            /** @var \App\Model\Entity\Vote $voteEntry */
            $voteEntry = $voteTable->newEmptyEntity();
            $voteEntry->user_id = $this->Auth->user('id');
            $voteEntry->application_id = $key;
            $voteEntry->funding_cycle_id = $fundingCycle->id;
            $voteEntry->weight = 1;
            if (!$voteTable->save($voteEntry)) {
                break;
            }
            $success = true;
        }
        if ($success) {
            $this->Flash->success(__('Your votes have successfully been submitted.'));

            return $this->redirect('/');
        }

        $this->Flash->error(__('Your votes could not be submitted.'));

        return $this->redirect(['action' => 'index']);
    }
}
