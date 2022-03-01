<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class UpdateUserChangePhoneTypeToVarchar extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('users');
        $table->changeColumn('phone', 'string', [
            'length' => 12
        ]);
        $table->update();
    }

    public function down()
    {
        $table = $this->table('users');
        $table->changeColumn('phone', 'biginteger', [
            'limit' => 12
        ]);
        $table->update();
    }
}
