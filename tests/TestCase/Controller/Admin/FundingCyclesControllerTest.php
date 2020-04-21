<?php
namespace App\Test\TestCase\Controller\Admin;

use App\Controller\Admin\FundingCyclesController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;

/**
 * App\Controller\Admin\FundingCyclesController Test Case
 *
 * @uses \App\Controller\Admin\FundingCyclesController
 */
class FundingCyclesControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.FundingCycles',
        'app.Applications',
        'app.Votes'
    ];

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->session([
            'Auth' => [
                'User' => [
                    'id' => 3,
                    'is_admin' => 1,
                    'is_verified' => 1
                ]
            ]
        ]);
        $this->get("/admin/funding-cycles");
        $this->assertResponseSuccess();
    }

    /**
     * Test add method
     *
     * @return void
     */
    public function testAdd()
    {
        $this->session([
            'Auth' => [
                'User' => [
                    'id' => 3,
                    'is_admin' => 1,
                    'is_verified' => 1
                ]
            ]
        ]);
        $data = [
            'application_begin' => 1572998643,
            'application_end' => 1572998644,
            'vote_begin' => 1572998645,
            'vote_end' => 1572998646,
            'funding_available' => 100
        ];
        $this->post("/admin/funding-cycles/add", $data);
        $this->assertResponseSuccess();
        $fundingCyclesTable = TableRegistry::getTableLocator()->get('fundingcycles');
        $query = $fundingCyclesTable->find()->where(['application_begin' => 1572998643]);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $this->session([
            'Auth' => [
                'User' => [
                    'id' => 3,
                    'is_admin' => 1,
                    'is_verified' => 1
                ]
            ]
        ]);
        $data = [
            'id' => 1,
            'funding_available' => 100
        ];
        $this->put("/admin/funding-cycles/edit/1", $data);
        $this->assertResponseSuccess();
        $fundingCyclesTable = TableRegistry::getTableLocator()->get('fundingcycles');
        $query = $fundingCyclesTable->find()->where(['id'=>1, 'funding_available' => 100]);
        $this->assertEquals(1, $query->count());
    }
}
