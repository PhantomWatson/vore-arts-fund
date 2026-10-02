<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Admin;

use App\Test\TestCase\LoginTrait;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\Admin\FundingCyclesController Test Case
 *
 * @uses \App\Controller\Admin\FundingCyclesController
 */
class FundingCyclesControllerTest extends TestCase
{
    use IntegrationTestTrait;
    use LoginTrait;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.Categories',
        'app.FundingCycles',
        'app.Projects',
        'app.Users',
        'app.Votes',
    ];

    /**
     * Returns form data for a funding cycle, with dates in the local timezone as submitted by the form
     *
     * @return array<string, mixed>
     */
    private function getFormData(): array
    {
        return [
            'application_begin' => '2030-01-01',
            'application_end' => '2030-01-31',
            'funding_available' => 100,
            'resubmit_deadline' => '2030-02-07',
            'vote_begin' => '2030-02-14',
            'vote_end' => '2030-02-28',
        ];
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex(): void
    {
        $this->loginAs(3);
        $this->get('/admin/funding-cycles');
        $this->assertResponseOk();
    }

    /**
     * Test add method
     *
     * @return void
     */
    public function testAdd(): void
    {
        $this->loginAs(3);
        $this->post('/admin/funding-cycles/add', $this->getFormData());
        $this->assertRedirect(['prefix' => 'Admin', 'controller' => 'FundingCycles', 'action' => 'index']);

        $count = TableRegistry::getTableLocator()
            ->get('FundingCycles')
            ->find()
            ->where(['funding_available' => 100])
            ->count();
        $this->assertSame(1, $count);
    }

    /**
     * Tests that a funding cycle can't be saved with its application period ending before it begins
     *
     * @return void
     */
    public function testAddWithInvalidDates(): void
    {
        $this->loginAs(3);
        $data = ['application_end' => '2029-12-01'] + $this->getFormData();
        $this->post('/admin/funding-cycles/add', $data);
        $this->assertNoRedirect();

        $count = TableRegistry::getTableLocator()
            ->get('FundingCycles')
            ->find()
            ->where(['funding_available' => 100])
            ->count();
        $this->assertSame(0, $count);
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit(): void
    {
        $this->loginAs(3);
        $this->put('/admin/funding-cycles/edit/1', $this->getFormData());
        $this->assertRedirect(['prefix' => 'Admin', 'controller' => 'FundingCycles', 'action' => 'index']);

        $fundingCycle = TableRegistry::getTableLocator()->get('FundingCycles')->get(1);
        $this->assertSame(100, $fundingCycle->funding_available);
    }
}
