<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * StatusesFixture
 */
class StatusesFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 6, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'name' => ['type' => 'string', 'length' => 200, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd
    /**
     * Init method
     *
     * @return void
     */
    public function init()
    {
        $this->records = [
            [
                'id' => 1,
                'name' => 'Applying'
            ],
            [
                'id' => 2,
                'name' => 'Accepted'
            ],
            [
                'id' => 3,
                'name' => 'Rejected'
            ],
            [
                'id' => 4,
                'name' => 'Revision Requested'
            ],
            [
                'id' => 5,
                'name' => 'Voting'
            ],
            [
                'id' => 6,
                'name' => 'Awarded'
            ],
            [
                'id' => 7,
                'name' => 'Not Awarded'
            ],
            [
                'id' => 8,
                'name' => 'Withdrawn'
            ],
            [
                'id' => 9,
                'name' => 'Under Review'
            ]
        ];
        parent::init();
    }
}
