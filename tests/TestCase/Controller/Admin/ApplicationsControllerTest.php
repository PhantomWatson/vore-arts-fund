<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Admin;

use Cake\ORM\TableRegistry;
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
        'app.Votes',
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
                    'is_verified' => 1,
                ],
            ],
        ]);
        $this->get('/admin/applications');
        $this->assertResponseSuccess();
    }

    /**
     * Test review method
     *
     * @return void
     */
    public function testReview()
    {
        $this->session([
            'Auth' => [
                'User' => [
                    'id' => 3,
                    'is_admin' => 1,
                    'is_verified' => 1,
                ],
            ],
        ]);
        $this->get('/admin/applications/review/1');
        $this->assertResponseSuccess();
    }

    /**
     * Test setStatus method
     *
     * @return void
     */
    public function testSetStatus()
    {
        $this->session([
            'Auth' => [
                'User' => [
                    'id' => 3,
                    'is_admin' => 1,
                    'is_verified' => 1,
                ],
            ],
        ]);
        $data = [
            'status_id' => 7,
        ];
        $this->post('/admin/applications/set-status/1', $data);
        $applicationsTable = TableRegistry::getTableLocator()->get('applications');
        $query = $applicationsTable->find()->where(['id' => 1, 'status_id' => 7]);
        $this->assertEquals(1, $query->count());
    }
}
