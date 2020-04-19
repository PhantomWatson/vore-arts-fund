<?php
namespace App\Test\TestCase\Controller;

use App\Controller\VotesController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\VotesController Test Case
 *
 * @uses \App\Controller\VotesController
 */
class VotesControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Votes',
        'app.Users',
        'app.Applications',
        'app.FundingCycles'
    ];

    /**
     * Test beforeFilter method
     *
     * @return void
     */
    public function testBeforeFilter()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

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
     * Test submit method
     *
     * @return void
     */
    public function testSubmit()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
