<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersFixture
 */
class UsersFixture extends TestFixture
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
        'email' => ['type' => 'string', 'length' => 200, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'password' => ['type' => 'string', 'length' => 200, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'phone' => ['type' => 'biginteger', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'is_admin' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'is_verified' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'created' => ['type' => 'timestamp', 'length' => null, 'null' => false, 'default' => 'CURRENT_TIMESTAMP', 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'timestamp', 'length' => null, 'null' => false, 'default' => 'CURRENT_TIMESTAMP', 'comment' => '', 'precision' => null],
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
                'name' => 'Unverified User',
                'email' => 'test@test.com',
                'password' => '$2y$10$7skNaC9tzOJ7GRrsdwBqV.qIsLQcLMc6iuT/D0mm.5.Sa9FZHenO.',
                'phone' => 1234567890,
                'is_admin' => 0,
                'is_verified' => 0,
                'created' => 1587180608,
                'modified' => 1587180608,
            ],
            [
                'id' => 2,
                'name' => 'Verified User',
                'email' => 'test1@test.com',
                'password' => 'b',
                'phone' => 1234567890,
                'is_admin' => 0,
                'is_verified' => 1,
                'created' => 1587180608,
                'modified' => 1587180608,
            ],
            [
                'id' => 3,
                'name' => 'Admin User',
                'email' => 'test2@test.com',
                'password' => 'c',
                'phone' => 1234567890,
                'is_admin' => 1,
                'is_verified' => 1,
                'created' => 1587180608,
                'modified' => 1587180608,
            ],
        ];
        parent::init();
    }
}
