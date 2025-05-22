<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddFinalizedAndLoanDisbursedDateToProjects extends AbstractMigration
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
        $table = $this->table('projects');
        $table->addColumn('loan_awarded_date', 'date', [
            'default' => null,
            'null' => true,
            'after' => 'tin',
        ]);
        $table->addColumn('is_finalized', 'boolean', [
            'default' => false,
            'null' => false,
            'after' => 'loan_awarded_date',
        ]);
        $table->update();
    }
}
