<?php
/**
 * Routes configuration.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * It's loaded within the context of `Application::routes()` method which
 * receives a `RouteBuilder` instance `$routes` as method argument.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

/** @var \Cake\Routing\RouteBuilder $routes */
$routes->setRouteClass(DashedRoute::class);

$routes->scope('/', function (RouteBuilder $builder) {
    // Pages
    $builder->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);
    $builder->connect('/about', 'Pages::about');
    $builder->connect('/contact', 'Pages::contact');
    $builder->connect('/pages/*', 'Pages::display');
    $builder->connect('/privacy', 'Pages::privacy');
    $builder->connect('/terms', 'Pages::terms');

    // Applications
    $builder->connect('/apply', 'Applications::apply');
    $builder->connect('/delete/:id', 'Applications::delete');
    $builder->connect('/resubmit/:id', 'Applications::resubmit');
    $builder->connect('/view-application/:id', 'Applications::view');
    $builder->connect('/withdraw/:id', 'Applications::withdraw');

    // Votes
    $builder->connect('/submit', 'Votes::submit');
    $builder->connect('/vote', 'Votes::index');
    $builder->connect('/vote', 'Votes::index');

    // Users
    $builder->connect('/change-account-info', 'Users::changeAccountInfo');
    $builder->connect('/forgot-password', 'Users::forgotPassword');
    $builder->connect('/login', 'Users::login');
    $builder->connect('/logout', 'Users::logout');
    $builder->connect('/my-account', 'Users::myAccount');
    $builder->connect('/register', 'Users::register');
    $builder->connect('/verify', 'Users::verify');
    $builder->connect('/verify/resend', 'Users::verifyResend');

    // Funding Cycles
    $builder->connect('/funding-cycles', 'FundingCycles::index');

    $builder->fallbacks(DashedRoute::class);
});

// Admin Routes
$routes->prefix('admin', function (RouteBuilder $builder) {
    // Admin
    $builder->connect('/', 'Admin::index');

    // Funding cycles
    $builder->connect('/funding-cycles', 'FundingCycles::index');
    $builder->connect('/funding-cycles/add', 'FundingCycles::add');
    $builder->connect('/funding-cycles/edit/:id', 'FundingCycles::edit');

    // Applications
    $builder->connect('/applications', 'Applications::index');
    $builder->connect('/applications/review/:id', 'Applications::review');
    $builder->connect('/applications/set-status/:id', 'Applications::setStatus');

    $builder->fallbacks(DashedRoute::class);
});
