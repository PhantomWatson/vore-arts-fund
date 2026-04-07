<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class ConvertAmountAwardedToCents extends BaseMigration
{
    public function up(): void
    {
        $this->execute('UPDATE projects SET amount_awarded = amount_awarded * 100 WHERE amount_awarded IS NOT NULL');
    }

    public function down(): void
    {
        $this->execute('UPDATE projects SET amount_awarded = amount_awarded / 100 WHERE amount_awarded IS NOT NULL');
    }
}
