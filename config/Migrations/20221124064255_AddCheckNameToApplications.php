<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddCheckNameToApplications extends AbstractMigration
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
        $table = $this->table('applications');
        $table->addColumn('check_name', 'string', [
            'default' => '',
            'limit' => 50,
            'null' => false,
            'after' => 'accept_partial_payout',
        ]);
        $table->update();
    }
}
