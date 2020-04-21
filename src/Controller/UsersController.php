<?php

namespace App\Controller;

use App\Model\Entity\User;
use App\Model\Table\UsersTable;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Event\Event;
use Cake\Http\Response;
use Cake\I18n\FrozenTime;
use Cake\Mailer\Email;
use Cake\Mailer\TransportFactory;
use Twilio\Rest\Client;
use Cake\ORM\TableRegistry;

/**
 * UsersController
 *
 * @property UsersTable $Users
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class UsersController extends AppController
{
    protected $allowCookie = true;
    protected $cookieTerm = '0';

    /**
     * beforeFilter callback method
     *
     * @param Event $event Event object
     * @return void
     */
    public function beforeFilter($event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow([
            'forgotPassword',
            'login',
            'logout',
            'register',
            'resetPasswordToken',
            'verify'
        ]);
        $this->Twilio = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));
    }

    /**
     * Users index page
     *
     * @return void
     */
    public function index()
    {
        $this->set('users', $this->Users->find('all'));
    }

    /**
     * Users view page
     *
     * @param int $id User ID
     * @return void
     */
    public function view($id)
    {
        $user = $this->Users->get($id);
        $this->set(compact('user'));
    }

    /**
     * Login page
     *
     * @return Response|null
     */
    public function login()
    {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            } else {
                $this->Flash->error(__('Invalid username or password, try again'));
            }
        }

        return null;
    }

    /**
     * User registration page
     *
     * @return Response|null
     */
    public function register()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            // admins should only be assignable from the database itself, new accounts always default to 0
            $user->is_admin = 0;
            // is_verified will later be assigned based on text verification API
            $user->is_verified = 0;
            if ($this->Users->save($user)) {
                if ($user->phone !== 1234567890)
                    $this->send($user->phone);
                $this->Flash->success(__('The user has been saved.'));
                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            } else {
                $this->Flash->error(__('Unable to register the user.'));
            }
        }
        $this->set('user', $user);

        return null;
    }


    public function send($phone)
    {
        $this->Twilio->verify->v2->services(env('TWILIO_SERVICE_SID'))->verifications->create("+1" . $phone, "sms");
    }

    public function validate($phone, $code)
    {
        $verification_check = $this->Twilio->verify->v2->services(env('TWILIO_SERVICE_SID'))->verificationChecks->create($code, ["to" => "+1" . $phone]);
        return $verification_check->status == 'approved';
    }

    /**
     * Logout page
     *
     * @return Response
     */
    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }

    /**
     * Forgot password page
     *
     * This page shows the user a form for having an email sent to them with a means to reset their password
     *
     * @param array ...$path Path segments
     * @return Response|null
     */
    public function forgotPassword(...$path)
    {
        if ($this->request->is('post')) {
            $user = $this->Users->findByEmail($this->request->getData()['User']['email'])->first();
            if (empty($user)) {
                $this->Flash->error('Sorry, the email address entered was not found.');

                return $this->redirect(['action' => 'forgotPassword']);
            } else {
                $user = $this->__generatePasswordToken($user);
                debug($user);
                if ($this->Users->save($user) && $this->__sendForgotPasswordEmail($user)) {
                    $this->Flash->success('Password reset instructions have been sent to your email address. You have 24 hours to complete the request.');

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
     * @return Response|null
     */
    public function resetPasswordToken($reset_password_token = null)
    {
        /** @var User $user */
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
     * Generates a password token for the specified user and then returns the modified user entity
     *
     * @param User $user User entity
     * @return User|null
     */
    private function __generatePasswordToken($user)
    {
        if (empty($user)) {
            return null;
        }

        // Generate a random string 100 chars in length.
        // $token = "";
        // for ($i = 0; $i < 6; $i++) {
        //     $d = rand(1, 100000) % 2;
        //     $d ? $token .= chr(rand(33,79)) : $token .= chr(rand(80,126));
        // }

        // (rand(1, 100000) % 2) ? $token = strrev($token) : $token = $token;

        // Generate hash of random string
        // $hash = (new DefaultPasswordHasher)->hash($token);

        $user = $this->Users->patchEntity($user, ['reset_password_token' => 1234]);
        // $user['User']['token_created_date']     = date('Y-m-d H:i:s');

        return $user;
    }

    /**
     * Returns TRUE if the time passed to it was within the last day
     *
     * @param FrozenTime $tokenCreatedDate The time that a token was generated
     * @return bool
     */
    private function __validToken($tokenCreatedDate)
    {
        return $tokenCreatedDate->wasWithinLast('24 hours');
    }

    /**
     * Sends an email with a link for resetting a password to the provided user
     *
     * @param User|null $user User entity
     * @return bool
     */
    private function __sendForgotPasswordEmail($user = null)
    {
        if (!empty($user)) {
            $email = new Email();
            $email
                ->setTo($user->email)
                ->setSubject('Password Reset Request - DO NOT REPLY')
                ->setReplyTo('noreply@voreartsfund.org')
                ->setFrom('noreply@voreartsfund.org')
                ->setTemplate('reset_password_request')
                ->setEmailFormat('html')
                ->setViewVars(['User' => $user])
                ->send();

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
    private function __sendPasswordChangedEmail($id = null)
    {
        if (!empty($id)) {
            $User = $this->Users->get($id);
            $email = new Email();
            $email
                ->setTo($User->email)
                ->setSubject('Password Changed - DO NOT REPLY')
                ->setReplyTo('noreply@voreartsfund.org')
                ->setFrom('noreply@voreartsfund.org', 'Do Not Reply')
                ->setTemplate('password_reset_success')
                ->setEmailFormat('both')
                ->setViewVars(['User' => $User]);
            $email->send();

            return true;
        }

        return false;
    }

    /**
     * User account verification page
     *
     * @param array ...$path Path segments
     * @return null
     */
    public function verify(...$path)
    {
        if ($this->request->is('post')) {
            $user = $this->Users->get($this->Auth->user('id'));
            $data = $this->request->getData();
            if ($this->validate($user['phone'], $data['code'])) {
                //success
                $this->redirect("/my-account");
            } else {
                $this->Flash->error(__('Error verifying phone'));
            }
        }
        return null;
    }

    /**
     * Page for resending a verification email
     *
     * @param array ...$path Path segments
     * @return null
     */
    public function verifyResend(...$path)
    {
        return null;
    }

    /**
     * "My Account" page
     *
     * @param array ...$path Path segments
     * @return null
     */
    public function myAccount(...$path)
    {
        return null;
    }

    /**
     * This page allows users to change their own account information
     *
     * @return Response|null
     */
    public function changeAccountInfo()
    {
        if ($this->request->is('post')) {
            $user = $this->Users->get($this->Auth->user('id'));

            $currentPassword = $this->request->getData('current_password');
            $passwordIsCorrect = (new DefaultPasswordHasher)->check($currentPassword, $user->password);
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
            debug($user);
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
