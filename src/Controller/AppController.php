<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
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
    protected array $breadcrumbs = [];
    protected string $currentBreadcrumb = '';

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
     * @param \Cake\Event\EventInterface $event Event object
     * @return \Cake\Http\Response|void|null
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->addBreadcrumb('Home', '/');

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

    /**
     * Returns TRUE if the current user owns the specified application
     *
     * @param int $applicationId
     * @return bool
     */
    protected function isOwnApplication($applicationId): bool
    {
        if (!$applicationId) {
            return false;
        }

        /** @var \App\Model\Entity\User $user */
        $user = $this->Authentication->getIdentity();

        $applicationsTable = TableRegistry::getTableLocator()->get('Applications');

        return $applicationsTable->exists(['id' => $applicationId, 'user_id' => $user->id]);
    }

    /**
     * @param string $title
     * @param array|string $url
     * @return void
     */
    protected function addBreadcrumb($title, $url)
    {
        $this->breadcrumbs[] = [$title, $url];
    }

    /**
     * Used for adding a breadcrumb to be applied to all pages under this controller
     *
     * @param $title
     * @return void
     */
    protected function addControllerBreadcrumb($title = null)
    {
        if (!$title) {
            $title = $this->name;
        }

        $this->addBreadcrumb(
            $title,
            [
                'prefix' => $this->request->getParam('prefix'),
                'controller' => $this->name,
                'action' => 'index'
            ]
        );
    }

    /**
     * @param string $title
     * @return void
     */
    protected function setCurrentBreadcrumb($title)
    {
        $this->currentBreadcrumb = $title;
    }

    public function beforeRender(EventInterface $event)
    {
        parent::beforeRender($event);
        $this->set([
            'breadcrumbs' => $this->breadcrumbs,
            'currentBreadcrumb' => $this->currentBreadcrumb,
        ]);
    }

    /**
     * Return the names of the JS and CSS files that need to be loaded for a React app
     *
     * @return array[]
     */
    protected function getAppFiles($dir): array
    {
        $retval = [
            'js' => [],
            'css' => [],
        ];
        $dist = WWW_ROOT . $dir . DS . 'dist';

        // JS
        if ($_GET['webpack-dev'] ?? false) {
            $retval['js'][] = 'http://' . $_GET['webpack-dev'] . '/main.bundle.js';
        } else {
            $files = is_dir($dist) ? scandir($dist) : false;
            if ($files) {
                foreach ($files as $file) {
                    if (preg_match('/\.bundle\.js$/', $file) === 1) {
                        $retval['js'][] = "/$dir/dist/$file";
                    }
                }
            }
        }

        // CSS
        $stylesDir = $dist . DS . 'styles';
        $files = is_dir($stylesDir) ? scandir($stylesDir) : false;
        if ($files) {
            foreach ($files as $file) {
                if (preg_match('/\.css$/', $file) === 1) {
                    $retval['css'][] = "/$dir/dist/styles$file";
                }
            }
        }

        return $retval;
    }
}
