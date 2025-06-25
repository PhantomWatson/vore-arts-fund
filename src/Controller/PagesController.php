<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Project;
use App\Model\Entity\Transaction;
use App\Model\Table\FundingCyclesTable;
use App\Model\Table\UsersTable;
use App\Model\Table\VotesTable;
use Cake\Core\Configure;
use Cake\Database\Query;
use Cake\Event\EventInterface;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\ORM\TableRegistry;
use Cake\View\Exception\MissingTemplateException;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 *
 * @property \App\Model\Table\FundingCyclesTable $FundingCycles
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 */
class PagesController extends AppController
{
    /**
     * beforeFilter callback method
     *
     * @param \Cake\Event\EventInterface $event Event object
     * @return \Cake\Http\Response|void|null
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated([
            'about',
            'botCatcher',
            'contact',
            'display',
            'home',
            'privacy',
            'terms',
            'discountEligibility',
            'partners',
        ]);
    }

    /**
     * Displays a view
     *
     * @param string ...$path Path segments.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Http\Exception\ForbiddenException When a directory traversal attempt.
     * @throws \Cake\View\Exception\MissingTemplateException When the view file could not
     *   be found and in debug mode.
     * @throws \Cake\Http\Exception\NotFoundException When the view file could not
     *   be found and not in debug mode.
     * @throws \Cake\View\Exception\MissingTemplateException In debug mode.
     */
    public function display(string ...$path): ?Response
    {
        if (!$path) {
            return $this->redirect('/');
        }
        if (in_array('..', $path, true) || in_array('.', $path, true)) {
            throw new ForbiddenException();
        }
        $page = $subpage = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        $this->set(compact('page', 'subpage'));

        try {
            return $this->render(implode('/', $path));
        } catch (MissingTemplateException $exception) {
            if (Configure::read('debug')) {
                throw $exception;
            }
            throw new NotFoundException();
        }
    }

    /**
     * "About" page
     *
     * @return void
     */
    public function about()
    {
        $this->title('About the Vore Arts Fund');
        $this->setCurrentBreadcrumb('About');

        /** @var UsersTable $usersTable */
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $boardMembers = $usersTable->getBoardMembers();

        $this->set(compact('boardMembers'));
    }

    /**
     * Contact information page
     *
     * @return void
     */
    public function contact()
    {
        $this->title('Contact');
    }

    /**
     * Terms of use page
     *
     * @return void
     */
    public function terms()
    {
        $this->title('Terms');
    }

    /**
     * Privacy policy page
     *
     * @return void
     */
    public function privacy()
    {
        $this->title('Privacy Policy');
    }

    private function setupFundingCyclesOverview(): void
    {
        /** @var FundingCyclesTable $fundingCyclesTable */
        $fundingCyclesTable = $this->fetchTable('FundingCycles');

        $cycleCategories = [
            'currentApplying' => 'Now accepting applications',
            'currentVoting' => 'Public voting now underway',
            'nextVoting' => 'Upcoming public vote',
            'future' => 'Next application period',
        ];

        $fundingCycles = [];
        foreach (array_keys($cycleCategories) as $finder) {
            $cycle = $fundingCyclesTable
                ->find($finder)
                ->orderAsc('application_end')
                ->contain(['Projects'])
                ->first();

            if (!$cycle) {
                continue;
            }

            $fundingCycles[$finder] = $cycle;
        }

        // This category should override => this category, if they're the same
        $overrides = [
            'currentApplying' => 'nextVoting',
            'future' => 'nextVoting',
        ];
        foreach ($overrides as $keepCategory => $ditchCategory) {
            if (
                isset($fundingCycles[$keepCategory]) && isset($fundingCycles[$ditchCategory])
                && $fundingCycles[$keepCategory]->id == $fundingCycles[$ditchCategory]->id
            ) {
                unset($fundingCycles[$ditchCategory]);
            }
        }

        $this->set([
            'cycleCategories' => $cycleCategories,
            'fundingCycles' => $fundingCycles,
        ]);
    }

    /**
     * @return void
     */
    public function home()
    {
        $this->setupFundingCyclesOverview();

        $articlesTable = $this->fetchTable('Articles');
        $article = $articlesTable
            ->find()
            ->where(['Articles.is_published' => true])
            ->orderDesc('Articles.dated')
            ->first();

        $isStaging = str_contains($_SERVER['HTTP_HOST'], 'staging.');

        $this->set([
            'isStaging' => $isStaging,
            'title' => '',
            'article' => $article,
        ]);

        // Display beta testing message
        if ($isStaging) {
            $this->Flash->set(
                '<strong>Welcome beta testers!</strong> staging.voreartsfund.org is exclusively used for <em>fake</em> applications and testing new website features. Check out <a href="https://docs.google.com/document/d/1BtZUQg6w3LaaumHRdgMXJC8EVIEu3GhzELijZdlebaU/edit?usp=sharing">these instructions</a> for helping out this project!',
                ['escape' => false]
            );
        }
    }

    public function maintenanceMode(): void
    {
        $this->title('Hang tight! We\'re undergoing maintenance.');
    }

    /**
     * A simple 404 page to render for bot requests
     *
     * @return void
     */
    public function botCatcher()
    {
        $this->viewBuilder()->setLayout('ajax');
        $this->response = $this->response->withStatus(404);
    }

    public function discountEligibility()
    {
        $users = $this->fetchTable('Users')
            ->find('forDiscountEligibility')
            ->all();

        $this->set(compact('users'));
        $this->title('Funding recipients eligible for partner discounts');
    }

    public function partners()
    {
        $this->title('Community Partners');
    }

    public function checks()
    {
        $this->title('Paying by check');
    }
}
