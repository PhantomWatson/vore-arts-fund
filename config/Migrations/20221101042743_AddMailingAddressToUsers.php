<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddMailingAddressToUsers extends AbstractMigration
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
        $table = $this->table('users');
        $table->addColumn('address', 'string', [
            'default' => '',
            'limit' => 50,
            'null' => false,
            'after' => 'phone',
        ]);
        $table->addColumn('zipcode', 'string', [
            'default' => '',
            'limit' => 10,
            'null' => false,
            'after' => 'address',
        ]);
        $table->update();
    }
}
