<?php

namespace App\Controller\Api;

use Cake\Event\EventInterface;

class UsersController extends ApiController
{
    /**
     * @param \Cake\Event\EventInterface $event Event object
     * @return \Cake\Http\Response|void|null
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated([
            'hasSession',
            'login',
        ]);
    }
    public function hasSession()
    {
        $result = (bool)$this->getAuthUser();
        $this->set('hasSession', $result);
        $this->viewBuilder()->setOption('serialize', ['hasSession']);
        $this->viewBuilder()->setClassName('Json');
    }

    public function login()
    {
        $result = $this->Authentication->getResult();
        $isValid = $result->isValid();

        $this->set('result', $isValid);
        if (!$isValid) {
            $this->setResponse(
                $this->getResponse()->withStatus(400, 'Invalid email address or password')
            );
        }
        $this->viewBuilder()->setOption('serialize', ['result']);
        $this->viewBuilder()->setClassName('Json');
    }
}
