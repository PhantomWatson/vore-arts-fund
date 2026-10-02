<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ProjectImagesFixture
 */
class ProjectImagesFixture extends TestFixture
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
                'project_id' => 1,
                'filename' => 'project.jpg',
                'weight' => 1,
                'caption' => 'Lorem ipsum dolor sit amet',
                'created' => '2019-11-06 00:00:00',
            ],
        ];
        parent::init();
    }
}
