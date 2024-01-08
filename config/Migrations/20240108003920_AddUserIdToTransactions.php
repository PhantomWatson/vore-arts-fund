<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddUserIdToTransactions extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('transactions');

        $table->addColumn('user_id', 'integer', [
            'default' => null,
            'limit' => 6,
            'null' => true,
            'after' => 'project_id',
        ]);

        $table->update();
    }
}
