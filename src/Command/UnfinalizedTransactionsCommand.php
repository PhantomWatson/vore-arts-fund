<?php
declare(strict_types=1);

namespace App\Command;

use App\Alert\AdminAlert;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\ORM\TableRegistry;

/**
 * Checks for transactions that should be finalized by now but aren't
 */
class UnfinalizedTransactionsCommand extends Command
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
     * Checks for unfinalized transactions that are at least an hour old and sends an alert if any are found
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return void
     */
    public function execute(Arguments $args, ConsoleIo $io): void
    {
        $transactionsTable = TableRegistry::getTableLocator()->get('Transactions');

        // Get unfinalized transactions that were created at least one hour ago
        $unfinalizedTransactions = $transactionsTable->find()
            ->where([
                'Transactions.finalized' => false,
                'Transactions.created <=' => new \DateTime('-1 hour')
            ])
            ->all();

        if ($unfinalizedTransactions->count() > 0) {
            $io->out('Found ' . $unfinalizedTransactions->count() . ' unfinalized transactions created in the last day');
            foreach ($unfinalizedTransactions as $unfinalizedTransaction) {
                $io->out('- #' . $unfinalizedTransaction->id);
                $age = $unfinalizedTransaction->created->timeAgoInWords();
                AdminAlert::send("Transaction #$unfinalizedTransaction->id was created $age and is still unfinalized. Check to see if Stripe webhook requests for `charge.updated` are failing or need to be manually re-triggered. If necessary, set this transaction's `amount_net` manually and mark it finalized.");
            }
        } else {
            $io->out('No unfinalized transactions found.');
        }
    }
}
