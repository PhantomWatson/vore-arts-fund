<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class UpdateReportsColumnTypes extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function up(): void
    {
        $columns = ['user_id', 'project_id'];
        foreach ($columns as $column) {
            $this->execute("ALTER TABLE reports MODIFY $column INT(6)");
        }
    }

    public function down(): void
    {

    }
}
