<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Datasource\ConnectionManager;
use Cake\Auth\DefaultPasswordHasher;
use Cake\DataSource\EntityInterface;
use Cake\ORM\TableRegistry;
use Cake\Mailer\Email;


/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class UsersController extends AppController
{

    /**
     * Displays a view
     *
     * @param array ...$path Path segments.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Http\Exception\ForbiddenException When a directory traversal attempt.
     * @throws \Cake\Http\Exception\NotFoundException When the view file could not
     *   be found or \Cake\View\Exception\MissingTemplateException in debug mode.
     */

     var $name = 'Users';
     var $helpers = array('Html', 'Form', 'Time');
     var $uses = array('User');
     var $allowCookie = true;
     var $cookieTerm = '0';


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

    public function index()
    {
        $this->set('users', $this->Users->find('all'));
    }

    public function view($id)
    {
        $user = $this->Users->get($id);
        $this->set(compact('user'));
    }

    public function login() {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            } else {
                $this->Flash->error(__('Invalid username or password, try again'));
            }
        }
    }

    public function register() {
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
    }

    public function logout() {
        return $this->redirect($this->Auth->logout());
    }

    public function forgotPassword(...$path) {
        if($this->request->is('post')){
        $user = $this->Users->findByEmail($this->request->getData()['User']['email'])->first();
            if(empty($user)){
                $this->Flash->error('Sorry, the email address entered was not found.');
                $this->redirect(['action' => 'forgotPassword']);
            } else {
                $user = $this->__generatePasswordToken($user);
                debug($user);
                if($this->Users->save($user) && $this->__sendForgotPasswordEmail($user)){
                    $this->Flash->success('Password reset instructions have been sent to your email address. You have 24 hours to complete the request.');
                    $this->redirect(['action' => 'login']);
               }
            }
        }
    }

    public function reset_password_token($reset_password_token = null){
        if(empty($this->data)){
            $this->data = $this->Users->findByResetPasswordToken($reset_password_token);
            if(!empty($this->data['User']['reset_password_token']) && !empty($this->data['User']['token_created_at']) && $this->__validToken($this->data['User']['token_created_at'])){
                $this->data['User']['id'] = null;
                $_SESSION['token'] = $reset_password_token;
            } else {
                $this->Flash->error('The password reset request has either expired or is invalid');
                $this->redirect(['action' => 'login']);
            }
        } else {
            if($this->data['User']['reset_password_token'] != $_SESSION['token']){
                $this->Flash->error('The password reset request has either expired or is invalid');
                $this->redirect(['action' => 'login']);
            }

            $user = $this->Users->findByResetPasswordToken($this->data['User']['reset_password_token']);
            $this->Users->id = $user['User']['id'];

            if($this->Users->save($this->data, array('validate' => 'only'))){
                // $this->data['User']['reset_password_token'] = $this->data['User']['token_created_at'] = null;
                if($this->Users->save($this->data) && $this->__sendPasswordChangedEmail($user['User']['id'])){
                    unset($_SESSION['token']);
                    $this->Session->setflash('Your password was changed successfully. Please login to continue');
                    $this->redirect(['action' => 'login']);
                }
            }
        }
    }

    private function __generatePasswordToken($user) {
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

    private function __validToken($token_created_at) {
        $expired = strtotime($token_created_at) + 86400;
        $time = strtotime("now");
        if ($time < $expired) {
            return true;
        }
        return false;
    }


    private function __sendForgotPasswordEmail($user = null) {
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

    private function __sendPasswordChangedEmail($id = null) {
        if (!empty($id)) {
            $this->User->id = $id;
            $User = $this->User->read();

            $this->Email->to 		= $User['User']['email'];
            $this->Email->subject 	= 'Password Changed - DO NOT REPLY';
            $this->Email->replyTo 	= 'noreply@voreartsfund.org';
            $this->Email->from 		= 'Do Not Reply <noreply@voreartsfund.org>';
            $this->Email->template 	= 'password_reset_success';
            $this->Email->sendAs 	= 'both';
            $this->set('User', $User);
            $this->Email->send();

            return true;
        }
        return false;
    }


    public function verify(...$path) {
        return null;
    }

    public function verifyResend(...$path) {
        return null;
    }

    public function myAccount(...$path) {
        return null;
    }

    public function adminPage(...$path) {
        return null;
    }

    public function changeAccountInfo() {
        $user = $this->request->getSession()->read('Auth.User');
        if ($this->request->is('post')){
            $userID = $user['id'];
            $connection = ConnectionManager::get('default');
            $results = $connection->execute('SELECT password FROM users WHERE id = :id', ['id' => $userID])->fetchAll('assoc');
            $password = $results[0]['password'];
            $data = $this->request->data;
            $currentPassword = $data['current_password'];

            $newEmail = $data['email'];
            $newName = $data['name'];
            $newPhone = $data['phone'];
            $newPassword = $data['new_password'];

            if ((new DefaultPasswordHasher)->check($currentPassword, $password)){
                if((!($newEmail=== "" or $newEmail === " "))){
                    $connection->execute("UPDATE users set email = '$newEmail' where id = '$userID'");
                }
                if((!($newName=== "" or $newName === " "))){
                    $connection->execute("UPDATE users set name = '$newName' where id = '$userID'");
                }
                if((!($newPhone=== "" or $newPhone === " "))){
                    $connection->execute("UPDATE users set phone = '$newPhone' where id = '$userID'");
                }
                if((!($newPassword=== "" or $newPassword === " "))){
                    $newHashedPassword = (new DefaultPasswordHasher)->hash($newPassword);
                    $connection->execute("UPDATE users set password = '$newHashedPassword' where id = '$userID'");
                }
                return $this->redirect($this->Auth->redirectUrl());
            }else{
                $this->Flash->error(__('Unable to update account information, please make sure to enter old password'));
            }
        }
    }

}
