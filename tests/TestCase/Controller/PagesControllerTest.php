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
 * @since         1.2.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * PagesControllerTest class
 *
 * @uses \App\Controller\PagesController
 */
class PagesControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.ArticleImages',
        'app.Articles',
        'app.Categories',
        'app.FundingCycles',
        'app.Projects',
        'app.Users',
    ];

    /**
     * testMultipleGet method
     *
     * @return void
     */
    public function testMultipleGet(): void
    {
        $this->get('/');
        $this->assertResponseOk();
        $this->get('/');
        $this->assertResponseOk();
    }

    /**
     * Tests that the home page renders, including the latest published article
     *
     * @return void
     */
    public function testHome(): void
    {
        $this->get('/');
        $this->assertResponseOk();
        $this->assertResponseContains('The Vore Arts Fund');
        $this->assertResponseContains('Lorem ipsum dolor sit amet');
    }

    /**
     * @return array<array<string>>
     */
    public static function staticPageProvider(): array
    {
        return [
            ['/about'],
            ['/contact'],
            ['/privacy'],
            ['/terms'],
        ];
    }

    /**
     * Tests that static pages render for unauthenticated visitors
     *
     * @param string $url Page URL
     * @return void
     */
    #[DataProvider('staticPageProvider')]
    public function testStaticPage(string $url): void
    {
        $this->get($url);
        $this->assertResponseOk();
    }

    /**
     * Test that missing template renders 404 page in production
     *
     * @return void
     */
    public function testMissingTemplate(): void
    {
        Configure::write('debug', false);
        $this->get('/pages/display/not_existing');

        $this->assertResponseCode(404);
    }

    /**
     * Test that missing template in debug mode renders missing_template error page
     *
     * @return void
     */
    public function testMissingTemplateInDebug(): void
    {
        Configure::write('debug', true);
        $this->get('/pages/display/not_existing');

        $this->assertResponseFailure();
        $this->assertResponseContains('Missing Template');
        $this->assertResponseContains('not_existing.php');
    }

    /**
     * Test directory traversal protection
     *
     * @return void
     */
    public function testDirectoryTraversalProtection(): void
    {
        $this->get('/pages/display/../Layout/ajax');
        $this->assertResponseCode(403);
    }
}
