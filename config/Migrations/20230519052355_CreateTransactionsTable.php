<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateTransactionsTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('transactions');
        $table
            ->addColumn('type', 'integer', [
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('amount', 'integer', [
                'default' => null,
                'limit' => 6,
                'null' => true,
            ])
            ->addColumn('application_id', 'integer', [
                'default' => null,
                'limit' => 6,
                'null' => true,
            ])
            ->addColumn('meta', 'text', [
                'default' => null,
                'null' => false,
            ])
            ->addColumn('created', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->create();
    }
}
