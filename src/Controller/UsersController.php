<?php
declare(strict_types=1);

namespace App\Controller;

use App\Application;
use App\Event\MailListener;
use App\Model\Entity\User;
use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Response;
use Cake\I18n\FrozenTime;
use Cake\Mailer\Mailer;
use Cake\Routing\Router;
use ReCaptcha\ReCaptcha;
use ReCaptcha\RequestMethod\CurlPost;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

/**
 * UsersController
 *
 * @property \App\Model\Table\UsersTable $Users
 * @property \Twilio\Rest\Client $Twilio
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class UsersController extends AppController
{
    private string $verificationCodeSentMsg =
        'Check for a text message containing your registration verification code. ' .
        'This code will be valid for the next 10 minutes.';

    /**
     * beforeFilter callback method
     *
     * @param \Cake\Event\EventInterface $event Event object
     * @return \Cake\Http\Response|void|null
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated([
            'forgotPassword',
            'login',
            'logout',
            'register',
            'resetPasswordToken',
        ]);
    }

    /**
     * Users index page
     *
     * @return void
     */
    public function index()
    {
        $this->set('users', $this->Users->find());
    }

    /**
     * Users view page
     *
     * @param int $id User ID
     * @return void
     */
    public function view(int $id)
    {
        $user = $this->Users->get($id);
        $this->set(compact('user'));
    }

    /**
     * Login page
     *
     * @return \Cake\Http\Response|null
     */
    public function login(): ?Response
    {
        $result = $this->Authentication->getResult();
        if ($result->isValid()) {
            $target = $this->Authentication->getLoginRedirect();
            return $this->redirect($target ?? '/');
        }

        if ($this->request->is(['post']) && !$result->isValid()) {
            $this->Flash->error('Invalid email address or password');
        }

        $this->title('Login');

        return null;
    }

    /**
     * Checks the reCAPTCHA response, returns a boolean, and displays an error message on failure
     *
     * @return bool
     */
    public function recaptchaResponseIsValid(): bool
    {
        $captchaResponse = $_POST['g-recaptcha-response'];
        $recaptcha = new ReCaptcha(
            Configure::read('recaptcha.secretKey'),
            new CurlPost(),
        );

        $resp = $recaptcha->verify($captchaResponse, $this->getRequest()->clientIp());
        if ($resp->isSuccess()) {
            return true;
        }

        $this->Flash->error(
            "Your CAPTCHA response could not be verified. $this->errorTryAgainContactMsg Details: "
            . implode(', ', $resp->getErrorCodes()),
            ['escape' => false]
        );
        return false;
    }

    /**
     * User registration page
     *
     * @return \Cake\Http\Response|null
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function register(): ?Response
    {
        $user = $this->Users->newEmptyEntity();

        if ($this->request->is('post') && $this->recaptchaResponseIsValid()) {
            $data = $this->request->getData();
            $data['email'] = $data['registerEmail'] ?? '';
            $data['password'] = $data['registerPassword'] ?? '';

            $data['phone'] = User::cleanPhone($data['phone']);
            $user = $this->Users->patchEntity($user, $data);

            $data['email'] = strtolower(trim($data['email']));

            // admins should only be assignable from the database itself, new accounts always default to 0
            $user->is_admin = 0;
            // is_verified will later be assigned based on text verification API, see verify() below
            $user->is_verified = 0;

            if ($this->Users->save($user)) {
                $this->Authentication->setIdentity($user);
                return $this->redirect(['controller' => 'Users', 'action' => 'registered']);
            }

            $this->Flash->error(
                'There was an error registering your account. '
                . 'Look for details below, and <a href="/contact">contact us</a> if you need assistance.',
                ['escape' => false]
            );
        }

        // Clear password field
        $this->request = $this->request->withData('password', '');

        $this->set([
            'title' => 'Register an Account',
            'user' => $user
        ]);

        return null;
    }

    /**
     * The post-register screen shown to the user, which also conditionally sends a phone verification code
     *
     * @return void
     * @throws TwilioException
     */
    public function registered(): void
    {
        $user = $this->getAuthUser();
        $shouldVerifyPhone = Configure::read('enablePhoneVerification') && ($user->phone ?? false) && !$user->is_verified;
        $verificationCodeSent = $shouldVerifyPhone && $this->sendVerificationText((string)$user->phone);
        $verificationCodeSentMsg = $this->verificationCodeSentMsg;
        $shouldVerifyPhone = true;
        $verificationCodeSent = false;
        $this->title('Your account has been registered');
        $this->set(compact('user', 'shouldVerifyPhone', 'verificationCodeSent', 'verificationCodeSentMsg'));
    }

    /**
     * Sends a verification text message
     *
     * @param string $phone Phone number
     * @return bool
     * @throws \Twilio\Exceptions\TwilioException
     * @throws BadRequestException
     */
    private function sendVerificationText(string $phone): bool
    {
        $phone = User::cleanPhone($phone);
        $errorMsg = 'A verification code could not be sent to the provided phone number. ' .
            'In your account settings, please check your phone number and make sure it\'s correct, ' .
            'then request a verification code. ' . $this->errorContactMsg;
        if (!$phone) {
            $this->Flash->error($errorMsg, ['escape' => false]);
            return false;
        }

        $accountSid = Configure::read('twilio_account_sid');
        $authToken = Configure::read('twilio_auth_token');
        $twilio = new Client($accountSid, $authToken);
        $serviceSid = Configure::read('twilio_service_sid');
        try {
            $verification = $twilio
                ->verify
                ->v2
                ->services($serviceSid)
                ->verifications
                ->create(
                    '+1' . $phone,
                    'sms',
                );
        } catch (\Exception $e) {
            $this->Flash->error(
                $errorMsg . ' Details: ' . $e->getMessage(),
                ['escape' => false]
            );
            return false;
        }

        return true;
    }

    /**
     * Returns TRUE if the provided phone number has been validated with a specific code, FALSE otherwise
     *
     * @param string $phone Phone number
     * @param string $code The verification string
     * @return bool
     */
    private function validate(string $phone, string $code): bool
    {
        $accountSid = Configure::read('twilio_account_sid');
        $authToken = Configure::read('twilio_auth_token');
        $twilio = new Client($accountSid, $authToken);
        $serviceSid = Configure::read('twilio_service_sid');
        try {
            $verificationCheck = $twilio
                ->verify
                ->v2
                ->services($serviceSid)
                ->verificationChecks
                ->create([
                    'to' => '+1' . $phone,
                    'code' => $code,
                ]);
        } catch (TwilioException $e) {
            return false;
        }

        return $verificationCheck->status == 'approved';
    }

    /**
     * Logout page
     *
     * @return \Cake\Http\Response
     */
    public function logout(): Response
    {
        $result = $this->Authentication->getResult();

        // regardless of POST or GET, redirect if user is logged in
        if ($result->isValid()) {
            $this->Authentication->logout();
            return $this->redirect(['action' => 'login']);
        }

        return $this->redirect($this->referer());
    }

    /**
     * Forgot password page
     *
     * This page shows the user a form for having an email sent to them with a means to reset their password
     *
     * @return \Cake\Http\Response|null
     */
    public function forgotPassword(): ?Response
    {
        if ($this->request->is('post')) {
            /** @var \App\Model\Entity\User $user */
            $data = $this->request->getData();
            $data['User']['email'] = strtolower(trim($data['User']['email']));
            $user = $this->Users->findByEmail($data['User']['email'])->first();
            if (empty($user)) {
                $this->Flash->error('Sorry, the email address entered was not found.');

                return $this->redirect(['action' => 'forgotPassword']);
            } else {
                $user = $this->__generatePasswordToken($user);
                $this->Users->save($user);
                $this->__sendForgotPasswordEmail($user);
                $this->Flash->success(
                    'Password reset instructions have been sent to your email address. ' .
                    'You have 24 hours to complete the request.'
                );

                return $this->redirect(['action' => 'login']);
            }
        }

        $this->title('Forgot Password');

        return null;
    }

    /**
     * Password reset page
     *
     * This is the page that is linked to in the password-reset emails sent to users
     *
     * @param string $reset_password_token User-specific password reset token
     * @return \Cake\Http\Response|null
     */
    public function resetPasswordToken($reset_password_token = null): ?Response
    {
        /** @var \App\Model\Entity\User $user */
        $user = $this->Users->findByResetPasswordToken($reset_password_token)->first();
        if (!$user || !$this->__validToken($user->token_created_date)) {
            $this->Flash->error('The password reset request has either expired or is invalid');

            return $this->redirect(['action' => 'login']);
        }

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData() + [
                'reset_password_token' => null,
                'token_created_date' => null,
            ];
            $user = $this->Users->patchEntity($user, $data);
            if ($this->Users->save($user)) {
                $this->Authentication->setIdentity($user);
                $this->__sendPasswordChangedEmail($user->id);
                $this->Flash->success('Your password was changed successfully. Please log in to continue');

                return $this->redirect(['action' => 'login']);
            }
            $this->Flash->error(
                'There was an error updating your password. ' . $this->errorTryAgainContactMsg,
                ['escape' => false],
            );
        }

        $this->set(['user' => $user]);
        $this->title('Change Your Password');

        return null;
    }

    /**
     * Generates a password token for the user, updates token_created_date, and then returns the modified user entity
     *
     * @param \App\Model\Entity\User $user User entity
     * @return \App\Model\Entity\User|null
     */
    private function __generatePasswordToken(User $user): ?User
    {
        if (empty($user)) {
            return null;
        }

        return $this->Users->patchEntity($user, [
            'reset_password_token' => rand(1000000, 9999999),
            'token_created_date' => new FrozenTime(),
        ]);
    }

    /**
     * Returns TRUE if the time passed to it was within the last day
     *
     * @param \Cake\I18n\FrozenTime $tokenCreatedDate The time that a token was generated
     * @return bool
     */
    private function __validToken(FrozenTime $tokenCreatedDate): bool
    {
        return $tokenCreatedDate->wasWithinLast('24 hours');
    }

    /**
     * Sends an email with a link for resetting a password to the provided user
     *
     * @param \App\Model\Entity\User|null $user User entity
     * @return bool
     */
    private function __sendForgotPasswordEmail($user = null): bool
    {
        if (!empty($user)) {
            $email = new Mailer();
            $url = Router::url([
                'controller' => 'Users',
                'action' => 'resetPasswordToken',
                $user->reset_password_token,
            ], true);
            $email
                ->setTo($user->email)
                ->setSubject(MailListener::$subjectPrefix . 'Password Reset Request')
                ->setReplyTo(Configure::read('noReplyEmail'))
                ->setFrom(Configure::read('noReplyEmail'), 'Vore Arts Fund')
                ->setEmailFormat('both')
                ->setViewVars(compact('user', 'url'))
                ->viewBuilder()
                ->setTemplate('reset_password_request');
            $email->send();

            return true;
        }

        return false;
    }

    /**
     * Sends an email confirming that the specified user's password has been changed
     *
     * @param int $id User ID
     * @return bool
     */
    private function __sendPasswordChangedEmail($id = null): bool
    {
        if (!empty($id)) {
            $user = $this->Users->get($id);
            $email = new Mailer();
            $supportEmail = Configure::read('supportEmail');
            $email
                ->setTo($user->email)
                ->setSubject(MailListener::$subjectPrefix . 'Password Changed')
                ->setReplyTo(Configure::read('noReplyEmail'))
                ->setFrom(Configure::read('noReplyEmail'), 'Vore Arts Fund')
                ->setEmailFormat('both')
                ->setViewVars(compact('user', 'supportEmail'))
                ->viewBuilder()
                ->setTemplate('password_reset_success');
            $email->send();

            return true;
        }

        return false;
    }

    /**
     * User account verification page
     *
     * @return null
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function verify()
    {
        $this->addBreadcrumb('Account');
        $user = $this->getAuthUser();

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            if ($this->validate((string)$user->phone, $data['code'])) {
                $this->Users->patchEntity($user, ['is_verified' => 1]);
                $this->Users->save($user);
                $this->Authentication->setIdentity($user);
                $this->Flash->success('Your phone number is now verified');
                $this->redirect(['action' => 'account']);
            } else {
                $this->Flash->error(
                    'Error verifying phone number. Please check to make sure you correctly entered the verification code that was texted to you. If the verification code was sent more than ten minutes ago, then it has expired, and you\'ll need to request a new code.'
                );
            }
        }

        $this->title('Verify phone number');
        $this->set(compact('user'));

        return null;
    }

    /**
     * Action for resending a verification email
     *
     * @return Response
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function verifyResend()
    {
        if ($this->getRequest()->is('post')) {
            $user = $this->getAuthUser();
            if ($this->sendVerificationText((string)$user->phone)) {
                $this->Flash->success($this->verificationCodeSentMsg);
                return $this->redirect(['action' => 'verify']);
            }
            $this->Flash->error(
                'There was an error sending a new verification code to your phone. ' .
                'Please try again, and <a href="/contact">contact us</a> if you need assistance.',
                ['escape' => false]
            );
        }

        return $this->redirect(['action' => 'verify']);
    }

    /**
     * "My Account" page
     *
     * @return Response
     */
    public function account(): Response
    {
        return $this->redirect(['action' => 'changeAccountInfo']);
    }

    /**
     * This page allows users to change their own account information
     *
     * @return \Cake\Http\Response|null
     */
    public function changeAccountInfo(): ?Response
    {
        $this->addBreadcrumb('Account');
        $this->title('Update Account Info');
        $user = $this->getAuthUser();

        if ($this->request->is(['post', 'put'])) {

            $data = $this->request->getData();
            $data['email'] = strtolower(trim($data['email']));

            // Update user entity
            $user = $this->Users->patchEntity(
                $user,
                $data,
                ['fields' => ['email', 'name', 'phone']]
            );

            // Save changes
            if ($this->Users->save($user)) {
                $this->Authentication->setIdentity($user);
                $this->Flash->success('Account info updated');
            } else {
                $this->Flash->error(
                    'There was an error updating your account info. ' . $this->errorTryAgainContactMsg,
                    ['escape' => false]
                );
            }
        }

        $this->set(['user' => $user]);

        return null;
    }

    public function updatePassword()
    {
        $this->addBreadcrumb('Account');
        $this->title('Update Password');
        $user = $this->getAuthUser();

        $newPassword = $this->request->getData('new_password');
        if ($newPassword) {
            $currentPassword = $this->request->getData('current_password');
            $passwordIsCorrect = (new DefaultPasswordHasher())->check($currentPassword, $user->password);
            if (!$passwordIsCorrect) {
                $this->Flash->error(
                    'Unable to update account information. '
                    . 'Please make sure that your current password has been entered and is correct, and '
                    . '<a href="/contact">contact us</a> if you need assistance.',
                    ['escape' => false]
                );

                return null;
            }

            $user = $this->Users->patchEntity($user, ['password' => $newPassword]);

            // Save changes
            if ($this->Users->save($user)) {
                $this->Flash->success('Password updated');
            } else {
                pr($user->getErrors());
                $this->Flash->error(
                    'There was an error updating your password. ' . $this->errorTryAgainContactMsg,
                    ['escape' => false]
                );
            }
        }
        $this->set(['user' => $user]);
    }

    /**
     * Returns a User entity for a form
     *
     * This looks weird, but the first $user is always immediately generated with a "phone number is not unique" error
     *
     * @return User
     */
    private function getUserForForm()
    {
        $user = $this->getAuthUser();
        return $this->Users->get($user->id);
    }
}
