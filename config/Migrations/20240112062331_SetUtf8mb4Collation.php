<?php
declare(strict_types=1);

use Migrations\BaseMigration;
use Migrations\Db\Table\Column;

class SetUtf8mb4Collation extends BaseMigration
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
        // Update the database being migrated (omitting the name uses the connection's current database)
        $this->execute('ALTER DATABASE CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        // All tables with string columns
        $tables = [
            'answers',
            'categories',
            'email_queue',
            'images',
            'messages',
            'notes',
            'projects',
            'questions',
            'reports',
            'transactions',
            'users',
        ];
        foreach ($tables as $tableName) {
            // Skip tables from plugins that are no longer installed
            if (!$this->hasTable($tableName)) {
                continue;
            }

            // Update table
            $this->execute("ALTER TABLE $tableName CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            // Update columns
            $table = $this->table($tableName);
            $columns = $table->getColumns();
            foreach ($columns as $column) {
                $type = $column->getType();
                $columnName = $column->getName();
                if ($type == Column::STRING) {
                    $limit = $column->getLimit() ?? 255;
                    $this->execute("ALTER TABLE $tableName MODIFY $columnName VARCHAR($limit) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                } elseif ($type == Column::TEXT) {
                    $this->execute("ALTER TABLE $tableName MODIFY $columnName TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                }
            }
        }
    }

    public function down() {
        // NO GOING BACK
    }

    /* Uncomment for dry run
    public function execute(string $sql, array $params = []): int
    {
        echo $sql . PHP_EOL;
        return 1;
    }
    */
}
