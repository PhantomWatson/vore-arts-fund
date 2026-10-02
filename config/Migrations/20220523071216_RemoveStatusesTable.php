<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class RemoveStatusesTable extends BaseMigration
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
        // Intentionally a no-op. Under the old Phinx backend, this queued drop() was never saved, so the table was
        // actually removed by the DropStatuses migration
    }
}
