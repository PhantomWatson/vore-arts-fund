<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

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
    protected array $fixtures = [
        'app.Categories',
        'app.FundingCycles',
        'app.Projects',
        'app.Users',
        'app.Votes',
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
     * Tests the voting page when no funding cycle is currently voting
     *
     * @return void
     */
    public function testIndex(): void
    {
        $this->get('/vote');
        $this->assertResponseOk();
        $this->assertResponseContains('Check back later for voting info');
    }

    /**
     * Tests that requesting a nonexistent funding cycle redirects to the default voting page
     *
     * @return void
     */
    public function testIndexWithInvalidFundingCycle(): void
    {
        $this->get('/vote/999');
        $this->assertRedirect(['controller' => 'Votes', 'action' => 'index']);
    }
}
