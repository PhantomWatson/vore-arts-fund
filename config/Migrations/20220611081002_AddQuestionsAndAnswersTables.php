<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddQuestionsAndAnswersTables extends BaseMigration
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
        $qTable = $this->table('questions');
        $qTable
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

        $aTable = $this->table('answers');
        $aTable
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

        // Explicitly named to match the index that the old Phinx backend implicitly created for this foreign key
        $this->table('answers')
             ->addIndex(['application_id'], ['name' => 'application_id'])
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
