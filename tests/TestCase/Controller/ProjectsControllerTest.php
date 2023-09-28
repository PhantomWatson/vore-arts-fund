<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\ProjectsController Test Case
 *
 * @uses \App\Controller\ProjectsController
 */
class ProjectsControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Projects',
        'app.Users',
        'app.Categories',
        'app.FundingCycles',
        'app.Statuses',
        'app.Images',
        'app.Messages',
        'app.Notes',
        'app.Votes',
    ];

    /**
     * Test apply method
     *
     * @return void
     */
    public function testApply()
    {
        $data = [
            'id' => 32,
            'title' => 'Test',
            'description' => 'Praesent id massa id nisl venenatis
            lacinia. Aenean sit amet justo. Morbi ut odio.
            Cras mi pede, malesuada in, imperdiet et, commodo
            vulputate, justo.',
            'amount_requested' => 555555,
            'accept_partial_payout' => 0,
            'category_id' => 3,
            'user_id' => 1,
            'funding_cycle_id' => 1,
            'status_id' => 9,
        ];
        $this->session([
            'Auth' => [
                'User' => [
                    'id' => 1,
                    'username' => 'testing',
                    // other keys.
                ],
            ],
        ]);
        $this->post('/apply', $data);
        $this->assertResponseSuccess();
        $projectsTable = TableRegistry::getTableLocator()->get('projects');
        $query = $projectsTable->find()->where(['id' => 1]);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test view method
     *
     * @return void
     */
    public function testView()
    {
        $this->session([
            'Auth' => [
                'User' => [
                    'id' => 1,
                    'username' => 'testing',
                    // other keys.
                ],
            ],
        ]);
        $this->get('/view-project/1');
        $this->assertResponseOk();
    }

    /**
     * Test withdraw method
     *
     * @return void
     */
    public function testWithdraw()
    {
        $data = [
            'id' => 1,
            'title' => 'Test',
            'description' => 'Praesent id massa id nisl venenatis
            lacinia. Aenean sit amet justo. Morbi ut odio.
            Cras mi pede, malesuada in, imperdiet et, commodo
            vulputate, justo.',
            'amount_requested' => 555555,
            'accept_partial_payout' => 0,
            'category_id' => 3,
            'user_id' => 1,
            'funding_cycle_id' => 1,
            'status_id' => 9,
        ];
        $this->session([
            'Auth' => [
                'User' => [
                    'id' => 1,
                    'username' => 'testing',
                    // other keys.
                ],
            ],
        ]);
        $this->post('/apply', $data);
        $this->assertResponseSuccess();

        $data = [
            'status_id' => 8,
        ];
        $this->post('/withdraw/1', $data);
        $this->assertResponseSuccess();
        $projectsTable = TableRegistry::getTableLocator()->get('projects');
        $query = $projectsTable->find()->where(['status_id' => 8, 'id' => 1]);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test resubmit method
     *
     * @return void
     */
    public function testResubmit()
    {
        $data = [
            'id' => 1,
            'title' => 'Test',
            'description' => 'Praesent id massa id nisl venenatis
            lacinia. Aenean sit amet justo. Morbi ut odio.
            Cras mi pede, malesuada in, imperdiet et, commodo
            vulputate, justo.',
            'amount_requested' => 555555,
            'accept_partial_payout' => 0,
            'category_id' => 3,
            'user_id' => 1,
            'funding_cycle_id' => 1,
            'status_id' => 8,
        ];
        $this->session([
            'Auth' => [
                'User' => [
                    'id' => 1,
                    'username' => 'testing',
                    // other keys.
                ],
            ],
        ]);
        $this->post('/apply', $data);
        $this->assertResponseSuccess();

        $data = [
            'status_id' => 9,
        ];
        $this->post('/resubmit/1', $data);
        $this->assertResponseSuccess();
        $projectsTable = TableRegistry::getTableLocator()->get('projects');
        $query = $projectsTable->find()->where(['status_id' => 9, 'id' => 1]);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
