<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddBiosTable extends AbstractMigration
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
        $this->table('bios')
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('title', 'string', [
                'default' => '',
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('bio', 'string', [
                'default' => '',
                'limit' => 2000,
                'null' => false,
            ])
            ->addColumn('image', 'string', [
                'default' => '',
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('created', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'user_id',
                ]
            )
            ->create();
    }
}
