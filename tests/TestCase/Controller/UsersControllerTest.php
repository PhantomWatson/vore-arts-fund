<?php
namespace App\Test\TestCase\Controller;

use App\Controller\UsersController;
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
        'app.Votes'
    ];

    /**
     * Test beforeFilter method
     *
     * @return void
     */
    public function testBeforeFilter()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test view method
     *
     * @return void
     */
    public function testView()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test login method
     *
     * @return void
     */
    public function testLogin()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test register method
     *
     * @return void
     */
    public function testRegister()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test send method
     *
     * @return void
     */
    public function testSend()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validate method
     *
     * @return void
     */
    public function testValidate()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test logout method
     *
     * @return void
     */
    public function testLogout()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test forgotPassword method
     *
     * @return void
     */
    public function testForgotPassword()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test resetPasswordToken method
     *
     * @return void
     */
    public function testResetPasswordToken()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test verify method
     *
     * @return void
     */
    public function testVerify()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test verifyResend method
     *
     * @return void
     */
    public function testVerifyResend()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test myAccount method
     *
     * @return void
     */
    public function testMyAccount()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test changeAccountInfo method
     *
     * @return void
     */
    public function testChangeAccountInfo()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test withdraw method
     *
     * @return void
     */
    public function testWithdraw()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
