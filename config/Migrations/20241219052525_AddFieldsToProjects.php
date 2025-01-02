<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddFieldsToProjects extends AbstractMigration
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
        $table = $this->table('projects');
        $table
            ->addColumn(
                'amount_awarded',
                'integer',
                [
                    'default' => null,
                    'null' => true,
                    'after' => 'accept_partial_payout',
                ]
            )
            ->addColumn(
                'address',
                'string',
                [
                    'default' => '',
                    'limit' => 100,
                    'null' => false,
                    'after' => 'status_id',
                ]
            )
            ->addColumn(
                'zipcode',
                'string',
                [
                    'limit' => 10,
                    'default' => '',
                    'null' => false,
                    'after' => 'address',
                ]
            )
            ->addColumn(
                'loan_agreement_date',
                'datetime',
                [
                    'default' => null,
                    'null' => true,
                    'after' => 'zipcode',
                ]
            )
            ->addColumn(
                'loan_due_date',
                'datetime',
                [
                    'default' => null,
                    'null' => true,
                    'after' => 'loan_agreement_date',
                ]
            )
            ->addColumn(
                'loan_agreement_version',
                'smallinteger',
                [
                    'default' => null,
                    'null' => true,
                    'after' => 'loan_due_date',
                ]
            )
            ->addColumn(
                'tin',
                'text',
                [
                    'null' => true,
                    'after' => 'loan_agreement_version',
                    'default' => null,
                ]
            );
        $table->update();
    }
}
