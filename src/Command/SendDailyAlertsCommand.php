<?php
declare(strict_types=1);

namespace App\Command;

use App\Alert\Alert;
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
     * @return null|void|int The exit code or null for success
     * @throws ApiErrorException
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $this->fundingCyclesTable = TableRegistry::getTableLocator()->get('FundingCycles');
        $this->alertApplicationPeriodEnded();
        $this->alertVotingPeriodStarted();
        $this->alertVotingPeriodEnded();

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

        if ($cycle) {
            echo '- Found cycle, sending alert' . PHP_EOL;
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
        } else {
            echo '- No cycles found' . PHP_EOL;
        }
    }

    private function alertVotingPeriodStarted()
    {
        echo 'Checking for cycle with voting period that started in the last 24 hours' . PHP_EOL;

        // Look for a funding cycle whose vote_begin was in the last 24 hours
        $cycle = $this->fundingCyclesTable
            ->find('votingBeganToday')
            ->first();

        if ($cycle) {
            echo '- Found cycle, sending alert' . PHP_EOL;
            $alert = new Alert();
            $alert->addLine(
                sprintf(
                    'Voting period started for the %s funding cycle. Time to promote!',
                    $cycle->name,
                ),
            );
            $alert->send(Alert::TYPE_ADMIN);
        } else {
            echo '- No cycles found' . PHP_EOL;
        }
    }

    private function alertVotingPeriodEnded()
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

        if ($cycle) {
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
        } else {
            echo '- No cycles found' . PHP_EOL;
        }
    }
}
