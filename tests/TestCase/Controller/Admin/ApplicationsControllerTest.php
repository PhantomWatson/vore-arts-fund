<?php
namespace App\Test\TestCase\Controller\Admin;

use App\Controller\Admin\ApplicationsController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\Admin\ApplicationsController Test Case
 *
 * @uses \App\Controller\Admin\ApplicationsController
 */
class ApplicationsControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Applications',
        'app.Users',
        'app.Categories',
        'app.FundingCycles',
        'app.Statuses',
        'app.Images',
        'app.Messages',
        'app.Notes',
        'app.Votes'
    ];

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test review method
     *
     * @return void
     */
    public function testReview()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test setStatus method
     *
     * @return void
     */
    public function testSetStatus()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
