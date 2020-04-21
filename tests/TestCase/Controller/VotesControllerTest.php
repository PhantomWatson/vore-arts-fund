<?php
namespace App\Test\TestCase\Controller;

use App\Controller\VotesController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;


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
        $data = [
            'id' => 1,
            'user_id' => 1,
            'application_id' => 1,
            'funding_cycle_id' => 1,
            'weight' => 1
        ];

        $this->post('/submit', $data);
        $this->assertResponseSuccess();
        $votesTable = TableRegistry::getTableLocator()->get('votes');
        $query = $votesTable->find()->where(['id' => 1]);
        $this->assertEquals(1, $query->count());


    }

}
