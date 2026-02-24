<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Model\Entity\FundingCycle;
use App\Model\Entity\Project;
use App\Model\Entity\Transaction;
use Cake\ORM\Query\SelectQuery;

/**
 * Transactions Controller
 *
 * @property \App\Model\Table\TransactionsTable $Transactions
 */
class TransactionsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated(['loanTransactions']);
    }

    public function loanHistory()
    {
        $fundingCyclesTable = $this->Transactions->Projects->FundingCycles;
        $fundingCycles = $fundingCyclesTable
            ->find()
            ->select(['id', 'application_begin'])
            ->where(['FundingCycles.is_finalized' => true])
            ->contain([
                'Projects' => function (SelectQuery $q) {
                    return $q
                        ->select(['id', 'funding_cycle_id'])
                        ->contain([
                            'Transactions' => function (SelectQuery $q) {
                                return $q
                                    ->select(['amount_net', 'project_id', 'type'])
                                    ->where(['type IN' => [
                                        Transaction::TYPE_LOAN,
                                        Transaction::TYPE_LOAN_REPAYMENT,
                                        Transaction::TYPE_CANCELED_CHECK,
                                    ]]);
                            },
                        ])
                        ->where(['Projects.status_id' => Project::STATUS_AWARDED_AND_DISBURSED]);
                },
            ])
            ->orderAsc('FundingCycles.application_begin')
            ->all();

        $bars = [];
        /** @var FundingCycle $fundingCycle */
        foreach ($fundingCycles as $fundingCycle) {
            $loanTotal = 0;
            $repaymentTotal = 0;

            foreach ($fundingCycle->projects as $project) {
                foreach ($project->transactions as $transaction) {
                    $amount = $transaction->amount_net / 100; // Convert from cents to dollars
                    if ($transaction->type === Transaction::TYPE_LOAN) {
                        $loanTotal += $amount;
                    } elseif ($transaction->type === Transaction::TYPE_LOAN_REPAYMENT) {
                        $repaymentTotal += $amount;
                    } elseif ($transaction->type === Transaction::TYPE_CANCELED_CHECK) {
                        // Canceled checks are treated as negative loans,
                        // since they represent money that was expected to be paid out but wasn't
                        $loanTotal -= $amount;
                    }
                }
            }

            if (!$loanTotal) {
                continue;
            }

            $bars[] = [
                'date' => $fundingCycle->application_begin,
                'loansOutstanding' => $loanTotal - $repaymentTotal,
                'loansRepaid' => $repaymentTotal,
            ];
        }

        $this->set(compact('bars'));
        $this->title('Loan history');
    }
}
