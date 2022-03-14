<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\UsersController Test Case
 *
 * @uses \App\Controller\UsersController
 */
class UsersControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Users',
        'app.Applications',
        'app.Messages',
        'app.Notes',
        'app.Votes',
    ];

    /**
     * Test login method
     *
     * @return void
     */
    public function testLogin()
    {
        $data = [
            'email' => 'test@test.com',
            'password' => 'Password',
        ];
        $this->post('/login', $data);
        $this->assertRedirect();
    }

    /**
     * Test register method
     *
     * @return void
     */
    public function testRegister()
    {
        $data = [
            'name' => 'Jimmy',
            'email' => 'test@voreartsfund.org',
            'password' => 'Password',
            'phone' => 1234567890,
        ];
        $this->post('/register', $data);
        $usersTable = TableRegistry::getTableLocator()->get('users');
        $query = $usersTable->find()->where(['email' => 'test@voreartsfund.org']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test logout method
     *
     * @return void
     */
    public function testLogout()
    {
        $this->session([
            'Auth' => [
                'User' => [
                    'id' => 3,
                    'is_admin' => 1,
                    'is_verified' => 1,
                ],
            ],
        ]);
        $this->get('/logout');
        $this->assertRedirect();
    }

    /**
     * Test myAccount method
     *
     * @return void
     */
    public function testMyAccount()
    {
        $this->session([
            'Auth' => [
                'User' => [
                    'id' => 1,
                    'is_admin' => 0,
                    'is_verified' => 1,
                ],
            ],
        ]);
        $this->get('/account');
        $this->assertResponseSuccess();
    }

    /**
     * Test changeAccountInfo method
     *
     * @return void
     */
    public function testChangeAccountInfo()
    {
        $this->session([
            'Auth' => [
                'User' => [
                    'id' => 1,
                    'is_admin' => 0,
                    'is_verified' => 0,
                ],
            ],
        ]);
        $data = [
            'name' => 'Joe',
            'current_password' => 'Password',
        ];
        $this->post('/account/update', $data);
        $this->assertResponseSuccess();
        $usersTable = TableRegistry::getTableLocator()->get('users');
        $query = $usersTable->find()->where(['id' => 1, 'name' => 'Joe']);
        $this->assertEquals(1, $query->count());
    }
}
