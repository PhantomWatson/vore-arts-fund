<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\FundingCyclesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\FundingCyclesTable Test Case
 */
class FundingCyclesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\FundingCyclesTable
     */
    public $FundingCycles;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.FundingCycles',
        'app.Applications',
        'app.Votes',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('FundingCycles') ? [] : ['className' => FundingCyclesTable::class];
        $this->FundingCycles = TableRegistry::getTableLocator()->get('FundingCycles', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->FundingCycles);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
