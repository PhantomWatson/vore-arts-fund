<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\User;
use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenTime;
use Cake\Mailer\Mailer;
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
    protected bool $allowCookie = true;
    protected string $cookieTerm = '0';

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
            'verify',
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
            return $this->redirect($target);
        }

        if ($this->request->is(['post']) && !$result->isValid()) {
            $this->Flash->error('Invalid email address or password');
        }

        return null;
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
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            // admins should only be assignable from the database itself, new accounts always default to 0
            $user->is_admin = 0;
            // is_verified will later be assigned based on text verification API, see verify() below
            $user->is_verified = 0;
            if ($this->Users->save($user)) {
                if ($user->phone !== 1234567890) {
                    $this->send((string)$user->phone);
                }
                $this->Flash->success('Your account has been registered');
                $this->Authentication->setIdentity($user);

                return $this->redirect(['action' => 'verify']);
            } else {
                $this->Flash->error('There was an error registering your account');
            }
        }
        $this->set('user', $user);

        return null;
    }

    /**
     * Sends a verification text message
     *
     * @param string $phone Phone number
     * @return void
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function send(string $phone)
    {
        $accountSid = Configure::read('twilio_account_sid');
        $authToken = Configure::read('twilio_auth_token');
        $twilio = new Client($accountSid, $authToken);
        $serviceSid = Configure::read('twilio_service_sid');
        $twilio->verify->v2->services($serviceSid)->verifications->create('+1' . $phone, 'sms');
    }

    /**
     * Returns TRUE if the provided phone number has been validated with a specific code, FALSE otherwise
     *
     * @param string $phone Phone number
     * @param string $code The verification string
     * @return bool
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function validate(string $phone, string $code): bool
    {
        $accountSid = Configure::read('twilio_account_sid');
        $authToken = Configure::read('twilio_auth_token');
        $twilio = new Client($accountSid, $authToken);
        $serviceSid = Configure::read('twilio_service_sid');
        $verification_check = $twilio
            ->verify
            ->v2
            ->services($serviceSid)
            ->verificationChecks
            ->create($code, ['to' => '+1' . $phone]);

        return $verification_check->status == 'approved';
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
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }

        $this->redirect($this->referer());
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
            $user = $this->Users->findByEmail($this->request->getData()['User']['email'])->first();
            if (empty($user)) {
                $this->Flash->error('Sorry, the email address entered was not found.');

                return $this->redirect(['action' => 'forgotPassword']);
            } else {
                $user = $this->__generatePasswordToken($user);
                if ($this->Users->save($user) && $this->__sendForgotPasswordEmail($user)) {
                    $this->Flash->success(
                        'Password reset instructions have been sent to your email address. ' .
                        'You have 24 hours to complete the request.'
                    );

                    return $this->redirect(['action' => 'login']);
                }
            }
        }

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

        if ($this->request->is('put')) {
            $data = $this->request->getData() + [
                'reset_password_token' => null,
                'token_created_date' => null,
            ];
            $user = $this->Users->patchEntity($user, $data);
            if ($this->Users->save($user)) {
                $this->__sendPasswordChangedEmail($user->id);
                $this->Flash->success('Your password was changed successfully. Please log in to continue');

                return $this->redirect(['action' => 'login']);
            }
            $this->Flash->error(
                'There was an error updating your password. ' .
                    'Please check for error messages, and contact an administrator if you need assistance.'
            );
        }

        $this->set(['user' => $user]);

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
            $subject = 'Password Reset Request - DO NOT REPLY';
            $email
                ->setTo($user->email)
                ->setSubject($subject)
                ->setReplyTo('noreply@voreartsfund.org')
                ->setFrom('noreply@voreartsfund.org')
                ->setEmailFormat('html')
                ->setViewVars(['User' => $user, 'subject' => $subject])
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
            $User = $this->Users->get($id);
            $email = new Mailer();
            $subject = 'Password Changed - DO NOT REPLY';
            $email
                ->setTo($User->email)
                ->setSubject($subject)
                ->setReplyTo('noreply@voreartsfund.org')
                ->setFrom('noreply@voreartsfund.org', 'Do Not Reply')
                ->setEmailFormat('both')
                ->setViewVars(['User' => $User, 'subject' => $subject])
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
        if ($this->request->is('post')) {
            $user = $this->request->getAttribute('identity');
            $data = $this->request->getData();
            if ($this->validate($user['phone'], $data['code'])) {
                //success
                $this->Users->patchEntity($user, [
                    'is_verified' => 1,
                ]);
                $this->Users->save($user);
                $this->redirect(['action' => 'myAccount']);
            } else {
                $this->Flash->error(__('Error verifying phone'));
            }
        }

        return null;
    }

    /**
     * Page for resending a verification email
     *
     * @return null
     */
    public function verifyResend()
    {
        return null;
    }

    /**
     * "My Account" page
     *
     * @return null
     */
    public function myAccount()
    {
        return null;
    }

    /**
     * This page allows users to change their own account information
     *
     * @return \Cake\Http\Response|null
     */
    public function changeAccountInfo(): ?Response
    {
        if ($this->request->is('post')) {
            $user = $this->request->getAttribute('identity');

            $currentPassword = $this->request->getData('current_password');
            $passwordIsCorrect = (new DefaultPasswordHasher())->check($currentPassword, $user->password);
            if (!$passwordIsCorrect) {
                $this->Flash->error(
                    'Unable to update account information. ' .
                        'Please make sure that your current password has been entered and is correct'
                );

                return null;
            }

            // Update user entity
            $fields = ['email', 'name', 'phone'];
            foreach ($fields as $field) {
                if ($this->request->getData($field)) {
                    $user = $this->Users->patchEntity($user, [$field => $this->request->getData($field)]);
                }
            }
            if ($this->request->getData('new_password')) {
                $user = $this->Users->patchEntity($user, ['password' => $this->request->getData('new_password')]);
            }

            // Save changes
            if ($this->Users->save($user)) {
                $this->Flash->success('Changes saved');
            } else {
                $this->Flash->error(
                    'There was an error saving those changes. ' .
                        'Please check for any error messages, and contact an administrator if you need assistance.'
                );
            }
        }

        return null;
    }
}
