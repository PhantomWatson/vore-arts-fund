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

use App\BotCatcher;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;
use Cake\Utility\Inflector;

function buildRoutes(RouteBuilder &$builder, array $controllers)
{
    foreach ($controllers as $controller => $actions) {
        foreach ($actions as $path => $action) {
            if (str_contains($path, '{id}')) {
                $builder->connect($path, ['controller' => $controller, 'action' => $action])
                    ->setPatterns(['id' => '\d+'])
                    ->setPass(['id']);
            } elseif (str_contains($path, '{slug}')) {
                $builder->connect($path, ['controller' => $controller, 'action' => $action])
                    ->setPatterns(['slug' => '[a-z0-9-]+'])
                    ->setPass(['slug']);
            } else {
                $builder->connect($path, ['controller' => $controller, 'action' => $action]);
            }
        }
    }
}

/** @var \Cake\Routing\RouteBuilder $routes */
$routes->setRouteClass(DashedRoute::class);

$routes->scope('/', function (RouteBuilder $builder) {
    $controllers = [
        'Pages' => [
            '/' => 'home',
            '/about' => 'about',
            '/contact' => 'contact',
            '/privacy' => 'privacy',
            '/terms' => 'terms',
            '/maintenance' => 'maintenanceMode',
            '/art-mart' => 'artMart',
            '/partners' => 'partners',
            '/discount-eligibility' => 'discountEligibility',
        ],
        'Projects' => [
            '/project/{id}' => 'view',
            '/apply' => 'apply',
        ],
        'Votes' => [
            '/vote' => 'index',
            '/vote/{id}' => 'index',
        ],
        'Users' => [
            '/register' => 'register',
            '/forgot-password' => 'forgotPassword',
            '/login' => 'login',
            '/logout' => 'logout',
            '/account' => 'account',
            '/account/update' => 'changeAccountInfo',
            '/account/password' => 'updatePassword',
            '/account/verify' => 'verify',
            '/account/verify/resend' => 'verifyResend',
        ],
        'FundingCycles' => [
            '/funding-cycles' => 'index',
            '/funding-cycle/{id}' => 'view',
        ],
        'Reports' => [
            '/report/{id}' => 'view',
            '/reports/for-project/{id}' => 'project',
            '/reports/submit/{id}' => 'submit',
        ],
        'Articles' => [
            '/news' => 'index',
            '/news/{slug}' => 'view',
        ],
    ];
    buildRoutes($builder, $controllers);

    BotCatcher::connectBotRoutes($builder);

    $builder->fallbacks(DashedRoute::class);
});

// "My Foo" routes
$routes->prefix('my', function (RouteBuilder $builder) {
    $controllers = [
        'Projects' => [
            '/projects'  => 'index',
            '/projects/{id}' => 'view',
            '/projects/messages/{id}' => 'messages',
            '/projects/edit/{id}' => 'edit',
            '/projects/delete/{id}' => 'delete',
            '/projects/withdraw/{id}' => 'withdraw',
            '/projects/loan-agreement/{id}' => 'loanAgreement',
            '/projects/verify-check-details/{id}' => 'verifyCheckDetails',
            '/projects/sign-loan-agreement/{id}' => 'signLoanAgreement',
            '/projects/view-loan-agreement/{id}' => 'viewLoanAgreement',
            '/projects/send-message/{id}' => 'sendMessage',
        ],
        'Bios' => [
            '/bio' => 'edit',
        ],
        'Loans' => [
            '/loans/payment/{id}' => 'payment',
        ],
        'Reports' => [
            '/reports/submit/{id}' => 'submit',
            '/reports/submit' => 'submit',
            '/reports/edit/{id}' => 'edit',
            '/reports/delete/{id}' => 'delete',
        ],
    ];
    buildRoutes($builder, $controllers);
    $builder->fallbacks(DashedRoute::class);
});

// Admin routes
$routes->prefix('admin', function (RouteBuilder $builder) {
    $controllers = [
        'Admin' => [
            '/' => 'index',
        ],
        'FundingCycles' => [
            '/funding-cycles' => 'index',
            '/funding-cycles/add' => 'add',
            '/funding-cycles/edit/{id}' => 'edit',
        ],
        'Projects' => [
            '/projects/{id}' => 'index', // Funding cycle ID
            '/projects' => 'index',
            '/projects/review/{id}' => 'review',
            '/projects/set-status/{id}' => 'setStatus',
            '/projects/new-note/{id}' => 'newNote',
            '/projects/get-tin/{id}' => 'getTin',
        ],
        'Questions' => [
            '/questions/edit/{id}' => 'edit',
            '/questions/delete/{id}' => 'delete',
        ],
        'Transactions' => [
            '/transactions/{id}' => 'view',
            '/transactions/edit/{id}' => 'edit',
            '/transactions/delete/{id}' => 'delete',
        ],
        'Votes' => [
            '/votes/{id}' => 'index',
        ],
    ];
    buildRoutes($builder, $controllers);

    $builder->fallbacks(DashedRoute::class);
});

$routes->prefix('api', function (RouteBuilder $builder) {
    $builder->connect('/projects/{action}', ['controller' => 'Projects']);
    $builder->connect('/votes/{action}', ['controller' => 'Votes']);
    $builder->connect('/stripe/{action}', ['controller' => 'Stripe']);
    $builder
        ->connect('/transactions', ['controller' => 'Transactions', 'action' => 'add'])
        ->setMethods(['POST']);
    $builder
        ->connect('/transactions/{id}', ['controller' => 'Transactions', 'action' => 'edit'])
        ->setPatterns(['id' => '\d+'])
        ->setPass(['id'])
        ->setMethods(['PATCH']);
    $builder
        ->connect('/transactions/{id}', ['controller' => 'Transactions', 'action' => 'delete'])
        ->setPatterns(['id' => '\d+'])
        ->setPass(['id'])
        ->setMethods(['DELETE']);
    $builder->connect('/transactions/{action}', ['controller' => 'Transactions']);
    $builder->fallbacks(DashedRoute::class);
});
