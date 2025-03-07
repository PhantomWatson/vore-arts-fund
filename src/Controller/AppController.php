<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\User;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\View\JsonView;

/**
 * Application Controller
 *
 * @link https://book.cakephp.org/4/en/controllers.html#the-app-controller
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 */
class AppController extends Controller
{
    protected array $breadcrumbs = [];
    protected string $currentBreadcrumb = '';

    protected string $errorContactMsg = 'Please <a href="/contact">contact us</a> if you need assistance.';
    protected string $errorTryAgainContactMsg =
        'Please try again, and <a href="/contact">contact us</a> if you need assistance.';

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
     * @return \Cake\Http\Response|null
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $isMaintenanceMode = Configure::read('maintenanceMode');
        $alreadyRedirected = $this->getRequest()->getParam('action') == 'maintenanceMode';
        if ($isMaintenanceMode && !$alreadyRedirected) {
            return $this->redirect([
                'controller' => 'Pages',
                'action' => 'maintenanceMode',
            ]);
        }

        $this->addBreadcrumb('Home', '/');
        return null;
    }

    /**
     * Sets view vars needed globally (e.g. for the navbar)
     *
     * @return void
     */
    protected function setGlobalViewVars(): void
    {
        $authUser = $this->getAuthUser();
        $isLoggedIn = (bool)$authUser;
        $isAdmin = $authUser->is_admin ?? false;
        $projectsTable = $this->getTableLocator()->get('Projects');
        $hasProjects = $authUser && $projectsTable->exists(['user_id' => $authUser->id]);

        $this->set(compact(
            'authUser',
            'hasProjects',
            'isAdmin',
            'isLoggedIn',
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
     * Returns TRUE if the current user owns the specified project
     *
     * @param int $projectId
     * @return bool
     */
    protected function isOwnProject($projectId): bool
    {
        if (!$projectId) {
            return false;
        }

        $user = $this->getAuthUser();
        if (!$user) {
            return false;
        }

        $projectsTable = TableRegistry::getTableLocator()->get('Projects');

        return $projectsTable->exists(['id' => $projectId, 'user_id' => $user->id]);
    }

    /**
     * @param string $title
     * @param array|string|null $url
     * @return void
     */
    protected function addBreadcrumb($title, $url = null)
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
        $this->setGlobalViewVars();
    }

    /**
     * Return the URL paths to the JS and CSS files that need to be loaded for a React app
     *
     * @param string $jsDir e.g. 'image-uploader/dist'
     * @param string $cssDir e.g. 'image-uploader/dist/styles'
     * @return array[]
     */
    protected function getAppFiles(string $jsDir, string $cssDir): array
    {
        $retval = [
            'js' => [],
            'css' => [],
        ];

        // JS
        if ($_GET['webpack-dev'] ?? false) {
            $retval['js'][] = 'http://' . $_GET['webpack-dev'] . '/main.bundle.js';
        } else {
            $files = is_dir(WWW_ROOT . $jsDir) ? scandir(WWW_ROOT . $jsDir) : [];
            foreach ($files as $file) {
                if (preg_match('/\.bundle\.js$/', $file) === 1) {
                    $retval['js'][] = "/$jsDir/$file";
                }
            }
        }

        // CSS
        $files = is_dir(WWW_ROOT . $cssDir) ? scandir(WWW_ROOT . $cssDir) : [];
        foreach ($files as $file) {
            if (preg_match('/\.css$/', $file) === 1) {
                $retval['css'][] = "/$cssDir/$file";
            }
        }

        return $retval;
    }

    /**
     * @return User|null
     */
    protected function getAuthUser()
    {
        return $this->Authentication->getIdentity()?->getOriginalData();
    }

    /**
     * Takes an entity and returns a string that can be used in flash messages to describe validation errors
     *
     * Useful for when error messages aren't automatically displayed in a form
     *
     * @param Entity $entity
     * @return string
     */
    protected function getEntityErrorDetails(Entity $entity): string
    {
        if (!$entity->hasErrors()) {
            return '';
        }

        return implode(
            '; ',
            array_map(
                function ($errors) {
                    return implode('; ', array_values($errors));
                },
                $entity->getErrors()
            ),
        );
    }

    protected function redirectToLogin(): ?\Cake\Http\Response
    {
        return $this->redirect([
            'controller' => 'Users',
            'action' => 'login',
            '?' => ['redirect' => $this->getRequest()->getPath()]
        ]);
    }
}
