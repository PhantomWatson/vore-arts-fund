<?php

/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
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

use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

/**
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 *
 * Cache: Routes are cached to improve performance, check the RoutingMiddleware
 * constructor in your `src/Application.php` file to change this behavior.
 *
 */
Router::defaultRouteClass(DashedRoute::class);

Router::scope('/', function (RouteBuilder $routes) {
    // Register scoped middleware for in scopes.
    // $routes->registerMiddleware('csrf', new CsrfProtectionMiddleware([
    //     'httpOnly' => true
    // ]));

    /**
     * Apply a middleware to the current route scope.
     * Requires middleware to be registered via `Application::routes()` with `registerMiddleware()`
     */
    // $routes->applyMiddleware('csrf');

    /**
     * Here, we are connecting '/' (base path) to a controller called 'Pages',
     * its action called 'display', and we pass a param to select the view file
     * to use (in this case, src/Template/Pages/home.ctp)...
     */
    $routes->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);

    /**
     * ...and connect the rest of 'Pages' controller's URLs.
     */
    $routes->connect('/apply', ['controller' => 'Applications', 'action' => 'apply']);
    $routes->connect('/vote', ['controller' => 'Votes', 'action' => 'index']);
    $routes->connect('/about', ['controller' => 'Pages', 'action' => 'about']);
    $routes->connect('/contact', ['controller' => 'Pages', 'action' => 'contact']);
    $routes->connect('/terms', ['controller' => 'Pages', 'action' => 'terms']);
    $routes->connect('/privacy', ['controller' => 'Pages', 'action' => 'privacy']);
    $routes->connect('/login', ['controller' => 'Users', 'action' => 'login']);
    $routes->connect('/register', ['controller' => 'Users', 'action' => 'register']);
    $routes->connect('/logout', ['controller' => 'Users', 'action' => 'logout']);
    $routes->connect('/forgot-password', ['controller' => 'Users', 'action' => 'forgotPassword']);
    $routes->connect('/my-account', ['controller' => 'Users', 'action' => 'myAccount']);
    $routes->connect('/verify', ['controller' => 'Users', 'action' => 'verify']);
    $routes->connect('/verify/resend', ['controller' => 'Users', 'action' => 'verifyResend']);
    $routes->connect('/change-account-info', ['controller' => 'Users', 'action' => 'changeAccountInfo']);
    $routes->connect('/view-application/:id', ['controller' => 'Applications', 'action' => 'view']);
    $routes->connect('/vote', ['controller' => 'Votes', 'action' => 'index']);
    $routes->connect('/submit', ['controller' => 'Votes', 'action' => 'submit']);
    $routes->connect('/withdraw/:id', ['controller' => 'Applications', 'action' => 'withdraw']);
    $routes->connect('/delete/:id', ['controller' => 'Applications', 'action' => 'delete']);
    $routes->connect('/resubmit/:id', ['controller' => 'Applications', 'action' => 'resubmit']);




    // Admin Routes
    Router::prefix('admin', function (RouteBuilder $routes) {
        // Because you are in the admin scope,
        // you do not need to include the  prefix
        // or the admin route element.
        $routes->connect('/', ['controller' => 'Admin', 'action' => 'index']);
        $routes->connect('/funding-cycles', ['controller' => 'FundingCycles', 'action' => 'index']);
        $routes->connect('/funding-cycles/add', ['controller' => 'FundingCycles', 'action' => 'add']);
        $routes->connect('/funding-cycles/edit/:id', ['controller' => 'FundingCycles', 'action' => 'edit']);
        $routes->connect('/applications', ['controller' => 'Applications', 'action' => 'index']);
        $routes->connect('/applications/review/:id', ['controller' => 'Applications', 'action' => 'review']);
        $routes->connect('/applications/set-status/:id', ['controller' => 'Applications', 'action' => 'setStatus']);
        $routes->fallbacks(DashedRoute::class);
    });




    /**
     * Connect catchall routes for all controllers.
     *
     * Using the argument `DashedRoute`, the `fallbacks` method is a shortcut for
     *
     * ```
     * $routes->connect('/:controller', ['action' => 'index'], ['routeClass' => 'DashedRoute']);
     * $routes->connect('/:controller/:action/*', [], ['routeClass' => 'DashedRoute']);
     * ```
     *
     * Any route class can be used with this method, such as:
     * - DashedRoute
     * - InflectedRoute
     * - Route
     * - Or your own route class
     *
     * You can remove these routes once you've connected the
     * routes you want in your application.
     */
    $routes->fallbacks(DashedRoute::class);
});

/**
 * If you need a different set of middleware or none at all,
 * open new scope and define routes there.
 *
 * ```
 * Router::scope('/api', function (RouteBuilder $routes) {
 *     // No $routes->applyMiddleware() here.
 *     // Connect API actions here.
 * });
 * ```
 */
