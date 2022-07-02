<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use App\Model\Entity\User;
use Cake\Event\EventInterface;
use Cake\Http\Exception\ForbiddenException;

class AdminController extends AppController
{
    /**
     * beforeFilter callback.
     *
     * @param \Cake\Event\EventInterface $event Event.
     * @return \Cake\Http\Response|null|void
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        /** @var User|null $user */
        $user = $this->Authentication->getIdentity();
        if (!($user->is_admin ?? false)) {
            throw new ForbiddenException();
        }
    }
}
