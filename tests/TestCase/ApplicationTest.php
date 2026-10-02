<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         3.3.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Test\TestCase;

use App\Application;
use App\Middleware\HostHeaderMiddleware;
use Authentication\Middleware\AuthenticationMiddleware;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\Middleware\BodyParserMiddleware;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * ApplicationTest class
 */
class ApplicationTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Tests that plugins required in every environment are loaded from config/plugins.php
     *
     * Bake, DebugKit, IdeHelper, and Migrations are excluded because they're only loaded in debug mode or the CLI
     *
     * @return void
     */
    public function testBootstrap(): void
    {
        $app = new Application(dirname(dirname(__DIR__)) . '/config');
        $app->bootstrap();
        $app->pluginBootstrap();
        $plugins = $app->getPlugins();

        $this->assertTrue($plugins->has('Authentication'));
        $this->assertTrue($plugins->has('Queue'));
    }

    /**
     * Tests the order of the middleware queue
     *
     * @return void
     */
    public function testMiddleware(): void
    {
        $app = new Application(dirname(dirname(__DIR__)) . '/config');
        $middleware = new MiddlewareQueue();

        $middleware = $app->middleware($middleware);

        $expected = [
            ErrorHandlerMiddleware::class,
            HostHeaderMiddleware::class,
            AssetMiddleware::class,
            RoutingMiddleware::class,
            BodyParserMiddleware::class,
            AuthenticationMiddleware::class,
        ];
        foreach ($expected as $position => $className) {
            $middleware->seek($position);
            $this->assertInstanceOf($className, $middleware->current());
        }
        $this->assertCount(count($expected), $middleware);
    }
}
