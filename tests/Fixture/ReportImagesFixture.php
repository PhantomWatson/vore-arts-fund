<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ReportImagesFixture
 */
class ReportImagesFixture extends TestFixture
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
                'report_id' => 1,
                'filename' => 'report.jpg',
                'weight' => 1,
                'caption' => 'Lorem ipsum dolor sit amet',
                'created' => '2022-11-26 07:53:51',
            ],
        ];
        parent::init();
    }
}
