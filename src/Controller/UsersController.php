<?php
namespace App\Controller;

use App\Model\Entity\User;
use App\Model\Table\UsersTable;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\Http\Response;
use Cake\Mailer\Email;

/**
 * UsersController
 *
 * @property \App\Model\Table\UsersTable $Users
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
            'reset_password_token',
            'verify'
        ]);
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
            $user->is_verified = 1;
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'register']);
            }
            $this->Flash->error(__('Unable to register the user.'));
        }
        $this->set('user', $user);

        return null;
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
    public function reset_password_token($reset_password_token = null)
    {
        if (empty($this->data)) {
            $this->data = $this->Users->findByResetPasswordToken($reset_password_token);
            if (!empty($this->data['User']['reset_password_token']) && !empty($this->data['User']['token_created_at']) && $this->__validToken($this->data['User']['token_created_at'])) {
                $this->data['User']['id'] = null;
                $_SESSION['token'] = $reset_password_token;
            } else {
                $this->Flash->error('The password reset request has either expired or is invalid');

                return $this->redirect(['action' => 'login']);
            }
        } else {
            if ($this->data['User']['reset_password_token'] != $_SESSION['token']) {
                $this->Flash->error('The password reset request has either expired or is invalid');

                return $this->redirect(['action' => 'login']);
            }

            $user = $this->Users->findByResetPasswordToken($this->data['User']['reset_password_token']);
            $this->Users->id = $user['User']['id'];

            if ($this->Users->save($this->data, ['validate' => 'only'])) {
                // $this->data['User']['reset_password_token'] = $this->data['User']['token_created_at'] = null;
                if ($this->Users->save($this->data) && $this->__sendPasswordChangedEmail($user['User']['id'])) {
                    unset($_SESSION['token']);
                    $this->Session->setflash('Your password was changed successfully. Please login to continue');

                    return $this->redirect(['action' => 'login']);
                }
            }
        }

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

        $user['reset_password_token'] = 1234;
        // $user['User']['token_created_at']     = date('Y-m-d H:i:s');

        return $user;
    }

    /**
     * Returns TRUE if the time passed to it was within the last day
     *
     * @param string $token_created_at A string representing the time that a token was generated
     * @return bool
     */
    private function __validToken($token_created_at)
    {
        $expired = strtotime($token_created_at) + 86400; // 24 hours
        $time = strtotime("now");
        if ($time < $expired) {
            return true;
        }

        return false;
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
                ->setTo($user['email'])
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
            $this->User->id = $id;
            $User = $this->User->read();

            $this->Email->to = $User['User']['email'];
            $this->Email->subject = 'Password Changed - DO NOT REPLY';
            $this->Email->replyTo = 'noreply@voreartsfund.org';
            $this->Email->from = 'Do Not Reply <noreply@voreartsfund.org>';
            $this->Email->template = 'password_reset_success';
            $this->Email->sendAs = 'both';
            $this->set('User', $User);
            $this->Email->send();

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
     * User administration page
     *
     * @param array ...$path Path segments
     * @return null
     */
    public function adminPage(...$path)
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
        $user = $this->request->getSession()->read('Auth.User');
        if ($this->request->is('post')) {
            $userID = $user['id'];
            $connection = ConnectionManager::get('default');
            $results = $connection->execute('SELECT password FROM users WHERE id = :id', ['id' => $userID])->fetchAll('assoc');
            $password = $results[0]['password'];
            $data = $this->request->getData();
            $currentPassword = $data['current_password'];

            $newEmail = $data['email'];
            $newName = $data['name'];
            $newPhone = $data['phone'];
            $newPassword = $data['new_password'];

            if ((new DefaultPasswordHasher)->check($currentPassword, $password)) {
                if ((!($newEmail === "" || $newEmail === " "))) {
                    $connection->execute("UPDATE users set email = '$newEmail' where id = '$userID'");
                }
                if ((!($newName === "" || $newName === " "))) {
                    $connection->execute("UPDATE users set name = '$newName' where id = '$userID'");
                }
                if ((!($newPhone === "" || $newPhone === " "))) {
                    $connection->execute("UPDATE users set phone = '$newPhone' where id = '$userID'");
                }
                if ((!($newPassword === "" || $newPassword === " "))) {
                    $newHashedPassword = (new DefaultPasswordHasher)->hash($newPassword);
                    $connection->execute("UPDATE users set password = '$newHashedPassword' where id = '$userID'");
                }

                return $this->redirect($this->Auth->redirectUrl());
            } else {
                $this->Flash->error(__('Unable to update account information, please make sure to enter old password'));
            }
        }

        return null;
    }
}
