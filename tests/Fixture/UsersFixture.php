<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersFixture
 */
class UsersFixture extends TestFixture
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
                'name' => 'Unverified User',
                'email' => 'test@test.com',
                'password' => '$2y$10$7skNaC9tzOJ7GRrsdwBqV.qIsLQcLMc6iuT/D0mm.5.Sa9FZHenO.',
                'phone' => '7655550001',
                'is_admin' => 0,
                'is_verified' => 0,
                'created' => '2020-04-18 03:30:08',
                'modified' => '2020-04-18 03:30:08',
            ],
            [
                'id' => 2,
                'name' => 'Verified User',
                'email' => 'test1@test.com',
                'password' => 'b',
                'phone' => '7655550002',
                'is_admin' => 0,
                'is_verified' => 1,
                'created' => '2020-04-18 03:30:08',
                'modified' => '2020-04-18 03:30:08',
            ],
            [
                'id' => 3,
                'name' => 'Admin User',
                'email' => 'test2@test.com',
                'password' => 'c',
                'phone' => '7655550003',
                'is_admin' => 1,
                'is_verified' => 1,
                'created' => '2020-04-18 03:30:08',
                'modified' => '2020-04-18 03:30:08',
            ],
        ];
        parent::init();
    }
}
