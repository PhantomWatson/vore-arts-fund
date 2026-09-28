<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateImageJoinTables extends BaseMigration
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
        $this->table('images_projects')
            ->addColumn('image_id', 'integer', ['null' => false])
            ->addColumn('project_id', 'integer', ['null' => false])
            ->addForeignKey('image_id', 'images', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->addForeignKey('project_id', 'projects', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
        $this->table('images_articles')
            ->addColumn('image_id', 'integer', ['null' => false])
            ->addColumn('article_id', 'integer', ['null' => false])
            ->addForeignKey('image_id', 'images', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->addForeignKey('article_id', 'articles', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
        $this->table('images_reports')
            ->addColumn('image_id', 'integer', ['null' => false])
            ->addColumn('report_id', 'integer', ['null' => false])
            ->addForeignKey('image_id', 'images', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->addForeignKey('report_id', 'reports', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();

        // For each of the existing images, we need to create a record in the join tables for each associated project, article, and report.
        $this->execute('INSERT INTO images_projects (image_id, project_id) SELECT id, project_id FROM images WHERE project_id IS NOT NULL');

        // Now delete the project_id column from the images table
        $this->table('images')
            ->removeColumn('project_id')
            ->update();
    }
}
