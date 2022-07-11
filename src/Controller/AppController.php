<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\EventInterface;
use Cake\View\JsonView;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/4/en/controllers.html#the-app-controller
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 */
class AppController extends Controller
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     * @throws \Exception
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        $this->loadComponent('Authentication.Authentication');

        /*
         * Enable the following component for recommended CakePHP form protection settings.
         * see https://book.cakephp.org/4/en/controllers/components/form-protection.html
         */
        //$this->loadComponent('FormProtection');
    }

    /**
     * The following function lists the pages accessible to visitors
     * who are not logged into a User account
     *
     * @param \Cake\Event\EventInterface $event Event object
     * @return \Cake\Http\Response|void|null
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        /** @var \App\Model\Entity\User|null $user */
        $user = $this->Authentication->getIdentity();
        $isLoggedIn = (bool)$user;
        $isAdmin = $user->is_admin ?? false;
        $isVerified = $user->is_verified ?? false;
        $applicationsTable = $this->getTableLocator()->get('Applications');
        $hasApplications = $user && $applicationsTable->exists(['user_id' => $user->id]);
        $this->set(compact(
            'hasApplications',
            'isAdmin',
            'isLoggedIn',
            'isVerified',
        ));
    }

    /**
     * Sets the page title
     *
     * @param string $title
     */
    protected function title(string $title)
    {
        $this->set(compact('title'));
    }

    /**
     * Sets supported view classes
     *
     * @return string[]
     */
    public function viewClasses(): array
    {
        return [JsonView::class];
    }
}
