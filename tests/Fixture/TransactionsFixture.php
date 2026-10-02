<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TransactionsFixture
 */
class TransactionsFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'type' => 1,
                'name' => 'Lorem ipsum dolor sit amet',
                'amount_gross' => 100,
                'amount_net' => 97,
                'project_id' => 1,
                'user_id' => 1,
                'meta' => 'Lorem ipsum dolor sit amet',
                'date' => '2023-05-19 05:33:11',
                'created' => '2023-05-19 05:33:11',
                'modified' => '2023-05-19 05:33:11',
            ],
        ];
        parent::init();
    }
}
