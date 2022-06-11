<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddQuestionsAndAnswersTables extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $qTable = $this->table('questions', ['id' => false, 'primary_key' => ['id']]);
        $qTable
            ->addColumn('id', 'integer', [
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('question', 'text', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('enabled', 'boolean', [
                'default' => true,
                'null' => false,
            ])
            ->addColumn('weight', 'integer', [
                'default' => 0,
                'limit' => 6,
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
            ->create();

        $aTable = $this->table('answers', ['id' => false, 'primary_key' => ['id']]);
        $aTable
            ->addColumn('id', 'integer', [
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('application_id', 'integer', [
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('question_id', 'integer', [
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('answer', 'string', [
                'default' => null,
                'limit' => 2000,
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
            ->create();

        $this->table('answers')
             ->addForeignKey(
                 'application_id',
                 'applications',
                 'id',
                 [
                     'update' => 'RESTRICT',
                     'delete' => 'RESTRICT'
                 ]
             )
             ->update();
    }
}
