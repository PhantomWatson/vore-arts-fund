<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class RenameApplicationsToProjects extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('applications');
        $table
            ->rename('projects')
            ->update();

        $linkedTables = [
            'answers',
            'images',
            'messages',
            'notes',
            'reports',
            'transactions',
            'votes',
        ];
        foreach ($linkedTables as $linkedTable) {
            $table = $this->table($linkedTable);
            $table
                ->renameColumn('application_id', 'project_id')
                ->update();
        }

        $indexedTables = [
            'answers',
            'images',
            'messages',
            'notes',
            'votes',
        ];
        foreach ($indexedTables as $indexedTable) {
            $table = $this->table($indexedTable);
            $table
                ->removeIndexByName('application_id')
                ->addIndex(['project_id'])
                ->update();
        }
    }
}
