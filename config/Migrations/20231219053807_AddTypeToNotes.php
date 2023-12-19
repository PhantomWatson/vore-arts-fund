<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddTypeToNotes extends AbstractMigration
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
        $table = $this->table('notes');
        $table->addColumn('type', 'string', [
            'default' => \App\Model\Entity\Note::TYPE_NOTE,
            'limit' => 20,
            'null' => false,
            'after' => 'project_id',
        ]);
        $table->update();
    }
}
