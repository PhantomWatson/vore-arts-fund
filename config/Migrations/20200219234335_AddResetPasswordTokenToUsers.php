<?php
use Migrations\AbstractMigration;

class AddResetPasswordTokenToUsers extends AbstractMigration
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
        $table->addColumn('reset_password_token', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => true,
            'after' => 'is_verified',
        ]);
        $table->update();
    }
}
