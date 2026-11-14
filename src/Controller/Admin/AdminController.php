<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Response;

class AdminController extends AppController
{
    /**
     * @return void
     */
    public function index()
    {
        $this->title('Admin Dashboard');
        $this->setCurrentBreadcrumb('Admin');
    }

    /**
     * beforeFilter callback.
     *
     * @param \Cake\Event\EventInterface $event Event.
     * @return Response|null
     */
    public function beforeFilter(EventInterface $event): ?Response
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return $this->redirect('/login');
        }
        if (!($user->is_admin ?? false)) {
            throw new ForbiddenException();
        }
        $this->addBreadcrumbAfterHome('Admin', ['controller' => 'Admin', 'action' => 'index']);

        return parent::beforeFilter($event);
    }
}
