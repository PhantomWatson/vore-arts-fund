<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Admin;

use App\Model\Entity\Project;
use App\Test\TestCase\LoginTrait;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\Admin\ProjectsController Test Case
 *
 * @uses \App\Controller\Admin\ProjectsController
 */
class ProjectsControllerTest extends TestCase
{
    use IntegrationTestTrait;
    use LoginTrait;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.Answers',
        'app.Categories',
        'app.FundingCycles',
        'app.Messages',
        'app.Notes',
        'app.ProjectImages',
        'app.Projects',
        'app.Questions',
        'app.Transactions',
        'app.Users',
        'app.Votes',
    ];

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex(): void
    {
        $this->loginAs(3);
        $this->get('/admin/projects');
        $this->assertResponseOk();
    }

    /**
     * Tests that non-admins can't access admin pages
     *
     * @return void
     */
    public function testIndexRequiresAdmin(): void
    {
        $this->loginAs(2);
        $this->get('/admin/projects');
        $this->assertResponseCode(403);
    }

    /**
     * Test review method
     *
     * @return void
     */
    public function testReview(): void
    {
        $this->loginAs(3);
        $this->get('/admin/projects/review/1');
        $this->assertResponseOk();
        $this->assertResponseContains('Lorem ipsum dolor sit amet');
    }

    /**
     * Tests changing a project's status through the review page
     *
     * @return void
     */
    public function testReviewSetStatus(): void
    {
        $this->loginAs(3);
        $this->post('/admin/projects/review/1', ['status_id' => Project::STATUS_ACCEPTED]);
        $this->assertRedirect(['prefix' => 'Admin', 'controller' => 'Projects', 'action' => 'review', 'id' => 1]);

        $project = TableRegistry::getTableLocator()->get('Projects')->get(1);
        $this->assertSame(Project::STATUS_ACCEPTED, $project->status_id);
    }

    /**
     * Tests that statuses that require a message to the applicant can't be set without one
     *
     * @return void
     */
    public function testReviewSetStatusRequiresMessage(): void
    {
        $this->loginAs(3);
        $this->post('/admin/projects/review/1', ['message' => '', 'status_id' => Project::STATUS_REJECTED]);
        $this->assertNoRedirect();

        $project = TableRegistry::getTableLocator()->get('Projects')->get(1);
        $this->assertSame(Project::STATUS_UNDER_REVIEW, $project->status_id);
    }
}
