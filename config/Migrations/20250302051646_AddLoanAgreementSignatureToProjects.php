<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddLoanAgreementSignatureToProjects extends AbstractMigration
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
        $table->addColumn('loan_agreement_signature', 'string', [
            'default' => '',
            'limit' => 100,
            'null' => false,
            'after' => 'zipcode',
        ]);
        $table->update();
    }
}
