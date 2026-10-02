<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Test\TestCase\LoginTrait;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use ReCaptcha\ReCaptcha;
use ReCaptcha\Response as ReCaptchaResponse;

/**
 * App\Controller\UsersController Test Case
 *
 * @uses \App\Controller\UsersController
 */
class UsersControllerTest extends TestCase
{
    use IntegrationTestTrait;
    use LoginTrait;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.Categories',
        'app.FundingCycles',
        'app.Messages',
        'app.Notes',
        'app.Projects',
        'app.Users',
        'app.Votes',
    ];

    /**
     * Replaces the reCAPTCHA client so that no request is sent to Google
     *
     * @param bool $isSuccess Whether verification should succeed
     * @return void
     */
    private function mockRecaptcha(bool $isSuccess): void
    {
        $this->mockService(ReCaptcha::class, function () use ($isSuccess) {
            $recaptcha = $this->createStub(ReCaptcha::class);
            $errorCodes = $isSuccess ? [] : ['invalid-input-response'];
            $recaptcha->method('verify')->willReturn(new ReCaptchaResponse($isSuccess, $errorCodes));

            return $recaptcha;
        });
    }

    /**
     * @return array<string, string>
     */
    private function getRegistrationData(): array
    {
        return [
            'g-recaptcha-response' => 'test',
            'name' => 'Jimmy',
            'phone' => '(765) 555-1234',
            'registerEmail' => 'test@voreartsfund.org',
            'registerPassword' => 'Password',
        ];
    }

    /**
     * Test login method
     *
     * @return void
     */
    public function testLogin(): void
    {
        $data = [
            'email' => 'test@test.com',
            'password' => 'Password',
        ];
        $this->post('/login', $data);
        $this->assertRedirect('/');
        $this->assertSession(1, 'Auth.id');
    }

    /**
     * Tests that an incorrect password does not log the user in
     *
     * @return void
     */
    public function testLoginWithWrongPassword(): void
    {
        $this->enableRetainFlashMessages();
        $data = [
            'email' => 'test@test.com',
            'password' => 'Wrong password',
        ];
        $this->post('/login', $data);
        $this->assertNoRedirect();
        $this->assertSessionNotHasKey('Auth');
        $this->assertFlashMessage('Invalid email address or password');
    }

    /**
     * Test register method
     *
     * @return void
     */
    public function testRegister(): void
    {
        $this->mockRecaptcha(true);
        $this->post('/register', $this->getRegistrationData());

        $this->assertRedirect(['controller' => 'Users', 'action' => 'registered']);
        $user = TableRegistry::getTableLocator()
            ->get('Users')
            ->find()
            ->where(['email' => 'test@voreartsfund.org'])
            ->first();
        $this->assertNotNull($user);
        $this->assertSame('7655551234', $user->phone);
        $this->assertFalse((bool)$user->is_admin);
        $this->assertFalse((bool)$user->is_verified);
    }

    /**
     * Tests that a user cannot make themselves an admin while registering
     *
     * @return void
     */
    public function testRegisterIgnoresIsAdmin(): void
    {
        $this->mockRecaptcha(true);
        $this->post('/register', ['is_admin' => 1] + $this->getRegistrationData());

        $user = TableRegistry::getTableLocator()
            ->get('Users')
            ->find()
            ->where(['email' => 'test@voreartsfund.org'])
            ->firstOrFail();
        $this->assertFalse((bool)$user->is_admin);
    }

    /**
     * Tests that a failed CAPTCHA prevents registration
     *
     * @return void
     */
    public function testRegisterWithFailedCaptcha(): void
    {
        $this->mockRecaptcha(false);
        $this->post('/register', $this->getRegistrationData());

        $this->assertNoRedirect();
        $count = TableRegistry::getTableLocator()
            ->get('Users')
            ->find()
            ->where(['email' => 'test@voreartsfund.org'])
            ->count();
        $this->assertSame(0, $count);
    }

    /**
     * Test logout method
     *
     * @return void
     */
    public function testLogout(): void
    {
        $this->loginAs(3);
        $this->get('/logout');
        $this->assertRedirect();
        $this->assertSessionNotHasKey('Auth');
    }

    /**
     * Tests that the account page redirects to the account info form
     *
     * @return void
     */
    public function testAccount(): void
    {
        $this->loginAs(1);
        $this->get('/account');
        $this->assertRedirect('/account/update');
    }

    /**
     * Tests that the account page requires logging in
     *
     * @return void
     */
    public function testAccountRequiresLogin(): void
    {
        $this->get('/account/update');
        $this->assertRedirectContains('/login');
    }

    /**
     * Test changeAccountInfo method
     *
     * @return void
     */
    public function testChangeAccountInfo(): void
    {
        $this->loginAs(1);
        $data = [
            'email' => ' Test@Test.com ',
            'name' => 'Joe',
            'phone' => '7655559876',
        ];
        $this->post('/account/update', $data);
        $this->assertResponseOk();

        $user = TableRegistry::getTableLocator()->get('Users')->get(1);
        $this->assertSame('Joe', $user->name);
        $this->assertSame('test@test.com', $user->email);
        $this->assertSame('7655559876', $user->phone);
    }
}
