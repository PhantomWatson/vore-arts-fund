<?php
use Migrations\AbstractMigration;

class AddTokenCreatedDateToUsers extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('users');
        $table->addColumn('token_created_date', 'datetime', [
            'default' => null,
            'null' => true,
            'after' => 'reset_password_token',
        ]);
        $table->update();
    }
}
