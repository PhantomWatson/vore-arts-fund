<?php
declare(strict_types=1);

namespace App\Command;

use App\Alert\Alert;
use App\Model\Entity\Project;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Stripe\Exception\ApiErrorException;

/**
 * SendDailyAlerts command
 *
 * Should be run daily (8am suggested) to send alerts to the admin team
 */
class SendDailyAlertsCommand extends Command
{
    private $fundingCyclesTable;

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
     * Implement this method with your command's logic.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return void
     * @throws ApiErrorException
     */
    public function execute(Arguments $args, ConsoleIo $io): void
    {
        $this->fundingCyclesTable = TableRegistry::getTableLocator()->get('FundingCycles');
        $this->alertApplicationPeriodEnded();
        $this->alertVotingPeriodStarted();
        $this->alertVotingPeriodEnded();
        $this->alertNoUpcomingCycle();
        $this->alertAnnualTasks();

        echo 'Done' . PHP_EOL;
    }

    private function alertApplicationPeriodEnded(): void
    {
        echo 'Checking for cycle with application period that ended in the last 24 hours' . PHP_EOL;

        // Look for a funding cycle whose application_end was in the last 24 hours
        $cycle = $this->fundingCyclesTable
            ->find()
            ->where(function (QueryExpression $exp) {
                return $exp
                    ->lt('application_end', date('Y-m-d H:i:s'))
                    ->gt('application_end', date('Y-m-d H:i:s', strtotime('-1 day')));
            })
            ->first();

        if (!$cycle) {
            echo '- No cycles found' . PHP_EOL;
            return;
        }

        echo '- Found cycle, sending "time to review" alert' . PHP_EOL;
        $alert = new Alert();
        $alert->addLine(
            sprintf(
                'Application period ended for the %s funding cycle. <%s|Time to review applications!>',
                $cycle->name,
                Router::url([
                    'prefix' => 'Admin',
                    'controller' => 'Projects',
                    'action' => 'index',
                    'id' => $cycle->id
                ], true),
            ),
        );
        $alert->send(Alert::TYPE_ADMIN);

        $currentlyApplying = $this->fundingCyclesTable->find('currentApplying')->first();
        if (!$currentlyApplying) {
            $alert = new Alert();
            $alert->addLine(
                sprintf(
                    'There\'s no funding cycle currently accepting applications. '
                    . '<%s|Create a new funding cycle> or alert the Muncie Arts and Culture council that we\'re not '
                    . 'currently accepting applications. (Otherwise, they\'ll continue implying that we\'re taking '
                    . 'applications in their mailing list messages.)',
                    Router::url([
                        'prefix' => 'Admin',
                        'controller' => 'FundingCycles',
                        'action' => 'add',
                    ], true),
                ),
            );
            $alert->send(Alert::TYPE_ADMIN);
        }
    }

    private function alertVotingPeriodStarted(): void
    {
        echo 'Checking for cycle with voting period that started in the last 24 hours' . PHP_EOL;

        // Look for a funding cycle whose vote_begin was in the last 24 hours
        $cycle = $this->fundingCyclesTable
            ->find('votingBeganToday')
            ->first();

        if (!$cycle) {
            echo '- No cycles found' . PHP_EOL;
            return;
        }

        $projectsTable = TableRegistry::getTableLocator()->get('Projects');
        $eligibleCount = $projectsTable
            ->find('eligibleForVoting', funding_cycle_id: $cycle->id)
            ->all()
            ->count();

        if ($eligibleCount === 0) {
            echo '- Found cycle, but no projects eligible for voting; skipping alert' . PHP_EOL;
            return;
        }

        echo '- Found cycle, sending alert' . PHP_EOL;
        $alert = new Alert();
        $alert->addLine(
            sprintf(
                'Voting period started for the %s funding cycle. Time to promote!',
                $cycle->name,
            ),
        );
        $alert->send(Alert::TYPE_ADMIN);
    }

    private function alertVotingPeriodEnded(): void
    {
        echo 'Checking for cycle with voting period that ended in the last 24 hours' . PHP_EOL;

        // Look for a funding cycle whose vote_end was in the last 24 hours
        $cycle = $this->fundingCyclesTable
            ->find()
            ->where(function (QueryExpression $exp) {
                return $exp
                    ->lt('vote_end', date('Y-m-d H:i:s'))
                    ->gt('vote_end', date('Y-m-d H:i:s', strtotime('-1 day')));
            })
            ->first();

        if (!$cycle) {
            echo '- No cycles found' . PHP_EOL;
            return;
        }

        $projectsTable = TableRegistry::getTableLocator()->get('Projects');
        $eligibleCount = $projectsTable
            ->find('eligibleForVoting', funding_cycle_id: $cycle->id)
            ->all()
            ->count();

        if ($eligibleCount === 0) {
            echo '- Found cycle, but no projects eligible for voting; skipping alert' . PHP_EOL;

            return;
        }

        echo '- Found cycle, sending alert' . PHP_EOL;
        $alert = new Alert();
        $alert->addLine(
            sprintf(
                'Voting period ended for the %s funding cycle. <%s|Time to review votes and award loans!>',
                $cycle->name,
                Router::url([
                    'prefix' => 'Admin',
                    'controller' => 'Votes',
                    'action' => 'index',
                    'id' => $cycle->id
                ], true),
            ),
        );
        $alert->send(Alert::TYPE_ADMIN);
    }

    private function alertNoUpcomingCycle(): void
    {
        echo 'Checking for a cycle with an application period ending in a week with no subsequent cycle' . PHP_EOL;

        // Look for a funding cycle whose application_end is in seven days
        $cycle = $this->fundingCyclesTable
            ->find()
            ->where(function (QueryExpression $exp) {
                return $exp
                    ->lt('application_end', date('Y-m-d H:i:s', strtotime('+8 days')))
                    ->gt('application_end', date('Y-m-d H:i:s', strtotime('+7 days')));
            })
            ->first();

        if ($cycle) {
            echo '- Found cycle, sending alert' . PHP_EOL;
            $alert = new Alert();
            $alert->addLine(
                sprintf(
                    'An application period will be ending soon, and there\'s no following application period. <%s|Add a funding cycle>',
                    Router::url([
                        'prefix' => 'Admin',
                        'controller' => 'FundingCycles',
                        'action' => 'add',
                        'id' => $cycle->id
                    ], true),
                ),
            );
            $alert->send(Alert::TYPE_ADMIN);
        } else {
            echo '- No cycles found' . PHP_EOL;
        }
    }

    private function alertAnnualTasks(): void
    {
        // Only run if this is New Year's Day
        if (date('m-d') !== '01-01') {
            return;
        }

        $alert = new Alert();
        $amount = '$' . number_format(Project::IRS_REPORTING_THRESHOLD / 100);
        $alert->addLine(
            "Happy New Year! Be sure to..."
        );
        $alert->addList([
            "Check with the IRS to confirm that the threshold for a forgiven loan needing to be reported as income is still $amount. If it isn't, update the value of `\App\Model\Entity\Project::IRS_REPORTING_THRESHOLD`.",
            "Prepare for the annual meeting of the board of directors"
        ]);
        $alert->send(Alert::TYPE_ADMIN);
    }
}
