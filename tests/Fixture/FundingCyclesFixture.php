<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * FundingCyclesFixture
 */
class FundingCyclesFixture extends TestFixture
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
                'application_begin' => '2020-02-01 00:00:00',
                'application_end' => '2020-04-30 00:00:00',
                'resubmit_deadline' => '2020-05-07 00:00:00',
                'vote_begin' => '2020-05-15 00:00:00',
                'vote_end' => '2020-05-30 00:00:00',
                'funding_available' => 1999999,
                'is_finalized' => 0,
                'created' => '2019-11-06 00:00:00',
                'modified' => '2019-11-06 00:00:00',
            ],
        ];
        parent::init();
    }
}
