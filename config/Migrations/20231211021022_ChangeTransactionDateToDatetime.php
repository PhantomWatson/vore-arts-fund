<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class ChangeTransactionDateToDatetime extends AbstractMigration
{
    public function up(): void
    {
        $this->table('transactions')
            ->changeColumn('date', 'datetime')
            ->save();
    }

    public function down(): void
    {
        $this->table('transactions')
            ->changeColumn('date', 'date')
            ->save();
    }
}
