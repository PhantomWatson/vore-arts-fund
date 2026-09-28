<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateMultipleImageTables extends BaseMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/5/guides/writing-migrations/migration-methods.html#the-change-method
     *
     * @return void
     */
    public function change(): void
    {
        // Create report_images and article_images tables
        $models = ['report', 'article'];
        foreach ($models as $model) {
            $table = $this->table("{$model}_images")
                ->addColumn("{$model}_id", 'integer', [
                    'default' => null,
                    'limit' => 6,
                    'null' => false,
                ])
                ->addColumn('filename', 'string', [
                    'default' => '',
                    'limit' => 50,
                    'null' => false,
                ])
                ->addColumn('weight', 'integer', [
                    'default' => null,
                    'limit' => 6,
                    'null' => false,
                ])
                ->addColumn('caption', 'string', [
                    'default' => '',
                    'limit' => 200,
                    'null' => false,
                ])
                ->addColumn('created', 'timestamp', [
                    'default' => 'CURRENT_TIMESTAMP',
                    'limit' => null,
                    'null' => false,
                ])
                ->addIndex(["{$model}_id"])
                ->addForeignKey(
                    "{$model}_id",
                    "{$model}s",
                    'id',
                    ['delete' => 'CASCADE'],
                );
            $table->create();
        }

        // Rename images table to project_images
        $this->table('images')
            ->rename('project_images')
            ->update();
    }
}
