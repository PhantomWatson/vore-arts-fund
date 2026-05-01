<?php
declare(strict_types=1);

namespace App\Command;

use App\Alert\ErrorAlert;
use App\Model\Entity\Project;
use App\Model\Entity\Transaction;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\TableRegistry;

/**
 * Scans the database to confirm data integrity for all projects
 */
class ProjectDataIntegrityCommand extends Command
{
    /**
     * Hook method for defining this command's option parser.
     *
     * @see https://book.cakephp.org/4/en/console-commands/commands.html#defining-arguments-and-options
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);

        return $parser;
    }

    /**
     * Checks for data integrity issues related to project awarded amounts and sends alerts for any errors found
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return void
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        // Find projects with awarded amounts that should be null but aren't
        $io->out('Looking for invalid non-null project awarded amounts...');
        $projectsTable = TableRegistry::getTableLocator()->get('Projects');
        $projects = $projectsTable
            ->find()
            ->where([
                'amount_awarded IS NOT' => null,
                'status_id NOT IN' => [
                    Project::STATUS_AWARDED_AND_DISBURSED,
                    Project::STATUS_AWARDED_NOT_YET_DISBURSED,
                ],
            ])
            ->all();
        foreach ($projects as $project) {
            $statusName = Project::getStatus($project->status_id);
            $awarded = number_format($project->amount_awarded / 100);
            $msg = "Project #$project->id has an awarded amount of $$awarded but a status of $statusName (not awarded)";
            $io->error("- $msg");
            ErrorAlert::send($msg);
        }
        $io->out('- Done', 2);

        // Find projects with no awarded amounts that should have a non-null value
        $io->out('Looking for invalid null project awarded amounts...');
        $projects = $projectsTable
            ->find()
            ->where([
                'amount_awarded IS' => null,
                'status_id IN' => [
                    Project::STATUS_AWARDED_AND_DISBURSED,
                    Project::STATUS_AWARDED_NOT_YET_DISBURSED,
                ],
            ])
            ->all();
        foreach ($projects as $project) {
            $statusName = Project::getStatus($project->status_id);
            $msg = "Project #$project->id has no awarded amount but has the $statusName status";
            $io->error($msg);
            ErrorAlert::send($msg);
        }
        $io->out('- Done', 2);

        // Check for project amount_awarded values that don't match the sum of LOAN-type transactions,
        // but ignore any project in a funding cycle that ended recently (because we may be waiting for a disbursement
        // to go out)
        $io->out('Looking for project awarded amounts that don\'t match transaction records...');
        $projects = $projectsTable
            ->find()
            ->where(['amount_awarded IS NOT' => null])
            ->contain([
                'Transactions' => function (SelectQuery $q) {
                    return $q->where(['Transactions.type' => Transaction::TYPE_LOAN]);
                }
            ])
            ->notMatching('FundingCycles', function (SelectQuery $q) {
                return $q->where(['FundingCycles.vote_end >=' => date('Y-m-d', strtotime('-1 month'))]);
            })
            ->all();
        foreach ($projects as $project) {
            $transactionTotal = array_reduce($project->transactions, function ($sum, $transaction) {
                return $sum + $transaction->amount_net;
            }, 0);
            if ($transactionTotal == $project->amount_awarded) {
                continue;
            }

            $awarded = number_format($project->amount_awarded / 100);
            $transactionTotal = number_format($transactionTotal / 100); // Convert from cents to dollars
            $msg = "Project #$project->id has an awarded amount of $$awarded but loan disbursement transaction records totalling $$transactionTotal";
            $io->error("- $msg");
            ErrorAlert::send($msg);
        }
        $io->out('- Done', 2);
    }
}
