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

    public function beforeFilter($event)
    {
      parent::beforeFilter($event);
      $this->Auth->allow('register', 'logout');
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

    public function login()
    {
      if ($this->request->is('post')) {
          $user = $this->Auth->identify();
          if ($user) {
              $this->Auth->setUser($user);
              return $this->redirect($this->Auth->redirectUrl());
          }
          $this->Flash->error(__('Invalid username or password, try again'));
      }
    }

    public function register() {
      $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
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
        return null;
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

}
