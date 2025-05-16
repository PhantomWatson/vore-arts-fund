<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateNudgesTable extends AbstractMigration
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
        $this
            ->table('nudges')
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'null' => false,
                'limit' => 11,
            ])
            ->addColumn('project_id', 'integer', [
                'default' => null,
                'null' => false,
                'limit' => 11,
            ])
            ->addColumn('type', 'integer', [
                'default' => null,
                'null' => false,
                'limit' => 11,
            ])
            ->addColumn('created', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'null' => false,
            ])
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'project_id',
                'projects',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->create();
    }
}
