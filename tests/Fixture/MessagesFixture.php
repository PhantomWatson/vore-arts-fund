<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * MessagesFixture
 */
class MessagesFixture extends TestFixture
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
                'body' => 'Lorem ipsum dolor sit amet',
                'is_unread' => 1,
                'created' => '2019-11-06 00:00:00',
            ],
        ];
        parent::init();
    }
}
