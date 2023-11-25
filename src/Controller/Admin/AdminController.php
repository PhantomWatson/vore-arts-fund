<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\Http\Exception\ForbiddenException;

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
     * @return \Cake\Http\Response|null|void
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $user = $this->getAuthUser();
        if (!($user->is_admin ?? false)) {
            throw new ForbiddenException();
        }
        $this->addBreadcrumb('Admin', ['controller' => 'Admin', 'action' => 'index']);
    }
}
