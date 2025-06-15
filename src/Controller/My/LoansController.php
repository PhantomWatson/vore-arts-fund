<?php

namespace App\Controller\My;

use App\Model\Entity\Transaction;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\ORM\TableRegistry;

/**
 * LoansController
 *
 * Handles loan-related actions for the user's projects.
 *
 * @property \App\Model\Table\ProjectsTable $Projects
 */
class LoansController extends \App\Controller\AppController
{
    /**
     * @param EventInterface $event
     * @return Response|null
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        if (!$this->getAuthUser()) {
            return $this->redirect(\App\Application::LOGIN_URL);
        }

        $projectId = $this->request->getParam('id');
        if ($projectId && !$this->isOwnProject($projectId)) {
            $this->Flash->error('Loan not found');
            $this->setResponse($this->getResponse()->withStatus(404));
            return $this->redirect('/');
        }

        $this->Projects = $this->fetchTable('Projects');

        $this->addControllerBreadcrumb('My Loans');

        return null;
    }
    public function view()
    {
        $projectId = $this->getRequest()->getParam('id');
        $project = $this->Projects->getNotDeleted($projectId);
        $repayments = TableRegistry::getTableLocator()->get('Transactions')
            ->find()
            ->where([
                'project_id' => $projectId,
                'type' => Transaction::TYPE_LOAN_REPAYMENT,
            ])
            ->toArray();

        $this->title('Loan repayment');
        $this->addBreadcrumb($project->title);
        $this->set(compact('project', 'repayments'));
    }
}
