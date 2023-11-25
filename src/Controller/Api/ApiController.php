<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\EventInterface;

/**
 * ApiController
 */
class ApiController extends AppController
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
        $preflightRequestCacheExpiration = 60 * 60;
        $this->response = $this->response->cors($this->request)
            ->allowOrigin(['http://localhost:3000'])
            ->allowMethods(['GET', 'POST', 'OPTIONS'])
            ->allowHeaders(['Accept', 'Content-Type', 'X-CSRF-Token'])
            ->allowCredentials()
            ->exposeHeaders(['Link'])
            ->maxAge(Configure::read('debug') ? 1 : $preflightRequestCacheExpiration)
            ->build();
    }
}
