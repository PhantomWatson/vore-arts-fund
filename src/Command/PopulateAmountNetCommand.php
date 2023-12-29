<?php
declare(strict_types=1);

namespace App\Command;

use App\Model\Entity\Transaction;
use App\Model\Table\TransactionsTable;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\TableRegistry;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

/**
 * PopulateAmountNet command
 *
 * Intended to be used when Stripe donations have amount_gross populated but amount_net empty
 * This will probably not be needed after amount_net is being recorded automatically
 */
class PopulateAmountNetCommand extends Command
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
     * Implement this method with your command's logic.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return null|void|int The exit code or null for success
     * @throws ApiErrorException
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        /** @var TransactionsTable $transactionsTable */
        $transactionsTable = TableRegistry::getTableLocator()->get('Transactions');
        $stripeTransactions = $transactionsTable
            ->find()
            ->where([
                function (QueryExpression $q) {
                    return $q->like('meta', '%balance_transaction%');
                },
                function (QueryExpression $q) {
                    return $q->isNull('amount_net');
                },
            ])
            ->all();

        /** @var Transaction $stripeTransaction */
        foreach ($stripeTransactions as $stripeTransaction) {
            $meta = json_decode($stripeTransaction->meta);
            echo 'Fetching details for balance transaction ' . $meta->balance_transaction . PHP_EOL;
            $netAmount = TransactionsTable::getNetAmount($meta->balance_transaction);
            if ($netAmount === null) {
                echo '- Net amount cannot be fetched. ApiErrorException encountered. Log in to Stripe and check logs for details.' . PHP_EOL;
                exit;
            }
            echo ' - Net amount is ' . $netAmount . PHP_EOL;
            $transactionsTable->patchEntity($stripeTransaction, ['amount_net' => $netAmount]);
            if ($transactionsTable->save($stripeTransaction)) {
                echo ' - Updated' . PHP_EOL;
            } else {
                echo 'Error updating transaction: ' . PHP_EOL;
                print_r($stripeTransaction->getErrors());
                exit;
            }
            echo PHP_EOL;
        }

        echo 'Done' . PHP_EOL;
    }
}
