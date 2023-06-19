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
    $builder->connect('/', ['controller' => 'Pages', 'action' => 'home']);
    $builder->connect('/about', ['controller' => 'Pages', 'action' => 'about']);
    $builder->connect('/contact', ['controller' => 'Pages', 'action' => 'contact']);
    $builder->connect('/privacy', ['controller' => 'Pages', 'action' => 'privacy']);
    $builder->connect('/terms', ['controller' => 'Pages', 'action' => 'terms']);

    // Applications
    $builder->connect('/application/{id}', ['controller' => 'Applications', 'action' => 'view']);
    $builder->connect('/apply', ['controller' => 'Applications', 'action' => 'apply']);

    // Votes
    $builder->connect('/submit', ['controller' => 'Votes', 'action' => 'submit']);
    $builder->connect('/vote', ['controller' => 'Votes', 'action' => 'index']);
    $builder->connect('/vote/{id}', ['controller' => 'Votes', 'action' => 'index'])
        ->setPatterns(['id' => '\d+'])
        ->setPass(['id']);

    // Users
    $builder->connect('/forgot-password', ['controller' => 'Users', 'action' => 'forgotPassword']);
    $builder->connect('/login', ['controller' => 'Users', 'action' => 'login']);
    $builder->connect('/logout', ['controller' => 'Users', 'action' => 'logout']);
    $builder->connect('/account', ['controller' => 'Users', 'action' => 'account']);
    $builder->connect('/account/update', ['controller' => 'Users', 'action' => 'changeAccountInfo']);
    $builder->connect('/register', ['controller' => 'Users', 'action' => 'register']);
    $builder->connect('/verify', ['controller' => 'Users', 'action' => 'verify']);
    $builder->connect('/verify/resend', ['controller' => 'Users', 'action' => 'verifyResend']);

    // Funding Cycles
    $builder->connect('/funding-cycles', ['controller' => 'FundingCycles', 'action' => 'index']);
    $builder->connect('/funding-cycle/{id}', ['controller' => 'FundingCycles', 'action' => 'view']);

    // Reports
    $builder->connect('/report/:id', ['controller' => 'Reports', 'action' => 'view'])
        ->setPatterns(['id' => '\d+'])
        ->setPass(['id']);
    $builder->connect('/reports/for-application/:id', ['controller' => 'Reports', 'action' => 'application'])
        ->setPatterns(['id' => '\d+'])
        ->setPass(['id']);
    $builder->connect('/reports/submit/:id', ['controller' => 'Reports', 'action' => 'submit'])
        ->setPatterns(['id' => '\d+'])
        ->setPass(['id']);

    $builder->fallbacks(DashedRoute::class);
});

// "My Foo" routes
$routes->prefix('my', function (RouteBuilder $builder) {
    $builder->connect('/applications', ['controller' => 'Applications', 'action' => 'index']);
    $builder->connect('/applications/{id}', ['controller' => 'Applications', 'action' => 'view'])
        ->setPatterns(['id' => '\d+'])
        ->setPass(['id']);
    $builder->connect('/applications/edit/{id}', ['controller' => 'Applications', 'action' => 'edit'])
        ->setPatterns(['id' => '\d+'])
        ->setPass(['id']);;
    $builder->connect('/applications/delete/{id}', ['controller' => 'Applications', 'action' => 'delete'])
        ->setPatterns(['id' => '\d+'])
        ->setPass(['id']);;
    $builder->connect('/applications/withdraw/{id}', ['controller' => 'Applications', 'action' => 'withdraw'])
        ->setPatterns(['id' => '\d+'])
        ->setPass(['id']);;
});

// Admin routes
$routes->prefix('admin', function (RouteBuilder $builder) {
    // Admin
    $builder->connect('/', ['controller' => 'Admin', 'action' => 'index']);

    // Funding cycles
    $builder->connect('/funding-cycles', ['controller' => 'FundingCycles', 'action' => 'index']);
    $builder->connect('/funding-cycles/add', ['controller' => 'FundingCycles', 'action' => 'add']);
    $builder->connect('/funding-cycles/edit/{id}', ['controller' => 'FundingCycles', 'action' => 'edit']);

    // Applications
    $builder->connect('/applications', ['controller' => 'Applications', 'action' => 'index']);
    $builder->connect('/applications/{id}', ['controller' => 'Applications', 'action' => 'index'])
        ->setPatterns(['id' => '\d+'])
        ->setPass(['id']);
    $builder->connect('/applications/review/{id}', ['controller' => 'Applications', 'action' => 'review']);
    $builder->connect('/applications/set-status/{id}', ['controller' => 'Applications', 'action' => 'setStatus']);

    $builder->fallbacks(DashedRoute::class);
});

$routes->prefix('api', function (RouteBuilder $builder) {
    $builder->connect('/applications/{action}', ['controller' => 'Applications']);
    $builder->connect('/votes/{action}', ['controller' => 'Votes']);
    $builder->connect('/stripe/{action}', ['controller' => 'Stripe']);
    $builder->connect('/transactions/{action}', ['controller' => 'Transactions']);
    $builder->fallbacks(DashedRoute::class);
});
