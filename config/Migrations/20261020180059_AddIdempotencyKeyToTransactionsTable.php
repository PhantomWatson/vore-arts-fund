<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddIdempotencyKeyToTransactionsTable extends BaseMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/5/guides/writing-migrations/migration-methods.html#the-change-method
     *
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('transactions');
        $table
            ->addColumn('stripe_event_id', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
                'after' => 'date',
            ])
            ->addIndex(['stripe_event_id'], ['unique' => true])
            ->update();
    }
}
