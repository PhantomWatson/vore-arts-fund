<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ReportsFixture
 */
class ReportsFixture extends TestFixture
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
                'body' => 'Lorem ipsum dolor sit amet',
                'user_id' => 1,
                'project_id' => 1,
                'is_final' => 1,
                'created' => '2022-11-26 07:53:51',
                'modified' => '2022-11-26 07:53:51',
            ],
        ];
        parent::init();
    }
}
