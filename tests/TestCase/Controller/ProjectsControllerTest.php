<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Model\Entity\Project;
use App\Test\TestCase\LoginTrait;
use Cake\I18n\DateTime;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\ProjectsController Test Case
 *
 * Also covers project actions in \App\Controller\My\ProjectsController
 *
 * @uses \App\Controller\ProjectsController
 * @uses \App\Controller\My\ProjectsController
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
        'app.Users',
        'app.Votes',
    ];

    /**
     * Creates a funding cycle that is currently accepting applications
     *
     * @return void
     */
    private function createCurrentFundingCycle(): void
    {
        $fundingCyclesTable = TableRegistry::getTableLocator()->get('FundingCycles');
        $fundingCycle = $fundingCyclesTable->newEntity([
            'application_begin' => new DateTime('-1 day'),
            'application_end' => new DateTime('+7 days'),
            'resubmit_deadline' => new DateTime('+10 days'),
            'vote_begin' => new DateTime('+14 days'),
            'vote_end' => new DateTime('+21 days'),
            'funding_available' => 1000000,
        ]);
        $fundingCyclesTable->saveOrFail($fundingCycle);
    }

    /**
     * @return array<string, mixed>
     */
    private function getApplicationData(): array
    {
        return [
            'accept_partial_payout' => 0,
            'address' => '123 Main St',
            'amount_requested' => 500,
            'answers' => [
                ['answer' => 'An answer', 'question_id' => 1],
            ],
            'category_id' => 1,
            'check_name' => 'Jimmy',
            'description' => 'A description',
            'eligibility-applicant-agree' => 1,
            'eligibility-project-agree' => 1,
            'loan-terms-agree' => 1,
            'save-mode' => 'submit',
            'title' => 'New project',
            'zipcode' => '47306',
        ];
    }

    /**
     * Sets the status of the project in ProjectsFixture
     *
     * @param int $statusId Status ID
     * @return void
     */
    private function setFixtureProjectStatus(int $statusId): void
    {
        TableRegistry::getTableLocator()->get('Projects')->updateAll(['status_id' => $statusId], ['id' => 1]);
    }

    /**
     * Tests that unauthenticated users are sent to log in before applying
     *
     * @return void
     */
    public function testApplyRequiresLogin(): void
    {
        $this->createCurrentFundingCycle();
        $this->get('/apply');
        $this->assertRedirectContains('/login');
    }

    /**
     * Tests the message shown when no funding cycle is accepting applications
     *
     * @return void
     */
    public function testApplyWithNoCurrentFundingCycle(): void
    {
        $this->loginAs(1);
        $this->get('/apply');
        $this->assertResponseOk();
        $this->assertResponseContains('applications are not being accepted at the moment');
    }

    /**
     * Test apply method
     *
     * @return void
     */
    public function testApply(): void
    {
        $this->createCurrentFundingCycle();
        $this->loginAs(1);
        $this->post('/apply', $this->getApplicationData());
        $this->assertRedirect(['prefix' => 'My', 'controller' => 'Projects', 'action' => 'index']);

        /** @var \App\Model\Entity\Project $project */
        $project = TableRegistry::getTableLocator()
            ->get('Projects')
            ->find()
            ->where(['title' => 'New project'])
            ->contain(['Answers'])
            ->firstOrFail();
        $this->assertSame(1, $project->user_id);
        $this->assertSame(Project::STATUS_UNDER_REVIEW, $project->status_id);
        $this->assertCount(1, $project->answers);
    }

    /**
     * Tests that applying fails unless all terms are agreed to
     *
     * @return void
     */
    public function testApplyRequiresAgreements(): void
    {
        $this->enableRetainFlashMessages();
        $this->createCurrentFundingCycle();
        $this->loginAs(1);
        $data = $this->getApplicationData();
        $data['loan-terms-agree'] = 0;
        $this->post('/apply', $data);
        $this->assertFlashMessage('You must agree to all terms in order to apply for funding.');

        $count = TableRegistry::getTableLocator()->get('Projects')->find()->where(['title' => 'New project'])->count();
        $this->assertSame(0, $count);
    }

    /**
     * Tests that an answer over the length limit is rejected and its error is displayed
     *
     * @return void
     */
    public function testApplyWithAnswerTooLong(): void
    {
        $this->createCurrentFundingCycle();
        $this->loginAs(1);
        $data = $this->getApplicationData();
        $data['answers'][0]['answer'] = str_repeat('a', 2001);
        $this->post('/apply', $data);
        $this->assertNoRedirect();
        $this->assertResponseContains('The provided value must be at most `2000` characters long');
    }

    /**
     * Tests that line breaks submitted as CRLF only count as one character toward the length limit
     *
     * @return void
     */
    public function testApplyCountsCrlfAsOneCharacter(): void
    {
        $this->createCurrentFundingCycle();
        $this->loginAs(1);
        $data = $this->getApplicationData();
        $data['answers'][0]['answer'] = str_repeat(str_repeat('a', 199) . "\r\n", 10);
        $this->post('/apply', $data);
        $this->assertRedirect(['prefix' => 'My', 'controller' => 'Projects', 'action' => 'index']);
    }

    /**
     * Test view method
     *
     * @return void
     */
    public function testView(): void
    {
        $this->setFixtureProjectStatus(Project::STATUS_ACCEPTED);
        $this->get('/project/1');
        $this->assertResponseOk();
        $this->assertResponseContains('Lorem ipsum dolor sit amet');
    }

    /**
     * Tests that projects that haven't been accepted can't be viewed publicly
     *
     * @return void
     */
    public function testViewUnderReview(): void
    {
        $this->setFixtureProjectStatus(Project::STATUS_UNDER_REVIEW);
        $this->get('/project/1');
        $this->assertRedirect('/');
    }

    /**
     * Test withdraw method
     *
     * @return void
     */
    public function testWithdraw(): void
    {
        $this->loginAs(1);
        $this->post('/my/projects/withdraw/1');
        $this->assertRedirect(['prefix' => 'My', 'controller' => 'Projects', 'action' => 'index']);

        $project = TableRegistry::getTableLocator()->get('Projects')->get(1);
        $this->assertSame(Project::STATUS_WITHDRAWN, $project->status_id);
    }

    /**
     * Tests that users can't withdraw other users' projects
     *
     * @return void
     */
    public function testWithdrawOtherUsersProject(): void
    {
        $this->loginAs(2);
        $this->post('/my/projects/withdraw/1');
        $this->assertRedirect('/');

        $project = TableRegistry::getTableLocator()->get('Projects')->get(1);
        $this->assertSame(Project::STATUS_UNDER_REVIEW, $project->status_id);
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete(): void
    {
        $this->setFixtureProjectStatus(Project::STATUS_DRAFT);
        $this->loginAs(1);
        $this->post('/my/projects/delete/1');
        $this->assertRedirect();

        $project = TableRegistry::getTableLocator()->get('Projects')->get(1);
        $this->assertSame(Project::STATUS_DELETED, $project->status_id);
    }

    /**
     * Tests that applications under review can't be deleted (they must be withdrawn first)
     *
     * @return void
     */
    public function testDeleteUnderReview(): void
    {
        $this->setFixtureProjectStatus(Project::STATUS_UNDER_REVIEW);
        $this->loginAs(1);
        $this->post('/my/projects/delete/1');

        $project = TableRegistry::getTableLocator()->get('Projects')->get(1);
        $this->assertSame(Project::STATUS_UNDER_REVIEW, $project->status_id);
    }
}
