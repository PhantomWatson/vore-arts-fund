<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class ChangeNoteTypeToInt extends BaseMigration
{
    public function up(): void
    {
        $table = $this->table('notes');
        $table->changeColumn('type', 'integer');
        $table->save();
    }

    public function down(): void
    {
        $table = $this->table('notes');
        $table->changeColumn('type', 'string', ['default' => '']);
        $table->save();
    }
}
