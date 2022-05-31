<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddResubmitDeadlineToFundingCycles extends AbstractMigration
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
        $table = $this->table('funding_cycles');
        $table->addColumn('resubmit_deadline', 'timestamp', [
            'default' => null,
            'null' => true,
            'after' => 'application_end',
        ]);

        $table->update();
    }
}
