<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * VotesFixture
 */
class VotesFixture extends TestFixture
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
                'user_id' => 1,
                'project_id' => 1,
                'funding_cycle_id' => 1,
                'weight' => 1,
                'created' => '2020-04-18 03:30:08',
                'modified' => '2020-04-18 03:30:08',
            ],
        ];
        parent::init();
    }
}
