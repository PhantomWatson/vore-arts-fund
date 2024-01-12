<?php
declare(strict_types=1);

use Cake\Core\Configure;
use Migrations\AbstractMigration;
use Phinx\Db\Table\Column;

class SetUtf8mb4Collation extends AbstractMigration
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
        // Load config info
        if (file_exists(CONFIG . 'environment.php')) {
            include(CONFIG . 'environment.php');
            $environment = getEnvironment();

            if (file_exists(CONFIG . 'app_local_' . $environment . '.php')) {
                Configure::load('app_local_' . $environment);
            }
        }

        // Update database
        $databaseConfig = Configure::read('Datasources');
        $dbName = $databaseConfig['default']['database'];
        $this->execute("ALTER DATABASE $dbName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

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
