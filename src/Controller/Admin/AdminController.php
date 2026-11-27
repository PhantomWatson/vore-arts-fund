<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Response;

class AdminController extends AppController
{
    /**
     * @return void
     */
    public function index()
    {
        $this->title('Admin Dashboard');
        $this->setCurrentBreadcrumb('Admin');

        // Funding cycles
        $fundingCyclesTable = $this->fetchTable('FundingCycles');
        $fundingCycles = [
            'takingApplications' => $fundingCyclesTable
                ->find('currentApplying')
                ->find('orderedAsc')
                ->all(),
            'voting' => $fundingCyclesTable
                ->find('currentVoting')
                ->find('orderedAsc')
                ->all(),
            'needsAwarding' => $fundingCyclesTable
                ->find('needsAwarding')
                ->find('orderedAsc')
                ->all(),
            'nextTakingApplications' => $fundingCyclesTable->find('nextApplying')->first(),
        ];

        // Applications
        /** @var \App\Model\Entity\FundingCycle|null $fundingCycle */
        $applications = [];
        $projectsTable = $this->fetchTable('Projects');
        if ($fundingCycles['takingApplications']->first()) {
            $applications = $projectsTable
                ->find('notDeleted')
                ->where(['Projects.funding_cycle_id' => $fundingCycles['takingApplications']->first()->id])
                ->contain(['Users'])
                ->all();
        }

        // Loans
        $totalOutstandingLoansQuery = $projectsTable
            ->find('withOutstandingLoan')
            ->orderByDesc('loan_awarded_date');
        $recentOutstandingLoans = $totalOutstandingLoansQuery->limit(10)->all();
        $outstandingLoanCount = $totalOutstandingLoansQuery->count();

        // Reports
        $recentReports = $this->fetchTable('Reports')
            ->find()
            ->contain(['Projects'])
            ->orderByDesc('Reports.created')
            ->limit(10)
            ->all();

        // Transactions
        $recentTransactions = $this->fetchTable('Transactions')
            ->find()
            ->contain(['Users'])
            ->orderByDesc('Transactions.created')
            ->limit(5)
            ->all();

        // Articles
        $article = $this->fetchTable('Articles')
            ->find()
            ->contain(['Images'])
            ->where(['is_published' => true])
            ->orderByDesc('dated')
            ->first();

        $this->set(compact(
            'applications',
            'article',
            'fundingCycles',
            'outstandingLoanCount',
            'recentOutstandingLoans',
            'recentReports',
            'recentTransactions',
        ));
    }

    /**
     * beforeFilter callback.
     *
     * @param \Cake\Event\EventInterface $event Event.
     * @return Response|null
     */
    public function beforeFilter(EventInterface $event): ?Response
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return $this->redirect('/login');
        }
        if (!($user->is_admin ?? false)) {
            throw new ForbiddenException();
        }
        $this->addBreadcrumbAfterHome('Admin', ['controller' => 'Admin', 'action' => 'index']);

        return parent::beforeFilter($event);
    }
}
