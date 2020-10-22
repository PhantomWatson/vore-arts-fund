<?php
/**
 * @var \App\View\AppView $this
 */
?>
  <?php
    /**
     * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
     * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
     *
     * Licensed under The MIT License
     * For full copyright and license information, please see the LICENSE.txt
     * Redistributions of files must retain the above copyright notice.
     *
     * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
     * @link          https://cakephp.org CakePHP(tm) Project
     * @since         0.10.0
     * @license       https://opensource.org/licenses/mit-license.php MIT License
     */

    use Cake\Cache\Cache;
    use Cake\Core\Configure;
    use Cake\Core\Plugin;
    use Cake\Datasource\ConnectionManager;
    use Cake\Error\Debugger;
    use Cake\Http\Exception\NotFoundException;

    $this->disableAutoLayout();

    $cakeDescription = 'CakePHP: the rapid development PHP framework';
    ?>
  <!DOCTYPE html>
  <html>

  <head>
      <?= $this->element('head'); ?>
      <title>
          <?= $cakeDescription ?>
      </title>
  </head>

  <body class="home">
      <?= $this->element('navbar'); ?>
      <div class="container">
          <div class='pb-2 mt-4 mb-2 border-bottom'>
              <h1>Login</h1>
          </div>

          <div class="users form">
              <?= $this->Flash->render() ?>
              <?= $this->Form->create() ?>
              <fieldset>
                  <?= $this->Form->control('email') ?>
                  <?= $this->Form->control('password') ?>
              </fieldset>
              <?= $this->Form->button(__('Login')); ?>
              <?= $this->Form->end() ?>
              <?= $this->Html->link('Forgot Password?', '/forgot-password', array('class' => 'button')); ?>
          </div>
      </div>
  </body>

  </html>