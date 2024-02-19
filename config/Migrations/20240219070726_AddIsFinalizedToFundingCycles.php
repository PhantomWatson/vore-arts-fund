<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddIsFinalizedToFundingCycles extends AbstractMigration
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
        $table = $this->table('funding_cycles');
        $table->addColumn('is_finalized', 'boolean', [
            'default' => false,
            'null' => false,
            'after' => 'funding_available',
        ]);
        $table->update();
    }
}
