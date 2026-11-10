<?php
declare(strict_types=1);

namespace App\Controller\Api;

use Cake\Event\EventInterface;
use Cake\Http\Response;

class MailingListController extends ApiController
{

    /**
     * beforeFilter callback method
     *
     * @param \Cake\Event\EventInterface $event Event object
     * @return \Cake\Http\Response|null
     */
    public function beforeFilter(EventInterface $event): ?Response
    {
        $this->Authentication->allowUnauthenticated([
            'vr-callback',
        ]);

        return parent::beforeFilter($event);
    }

    /**
     * POST /api/mailing-list/vr-callback endpoint (for Vertical Response)
     *
     * https://developer.verticalresponse.com/docs/read/Webhooks
     *
     * @return void
     */
    public function vrCallback(): void
    {
        $this->set([
            'result' => true
        ]);
        $this->viewBuilder()->setOption('serialize', ['result']);
        $this->viewBuilder()->setClassName('Json');
    }
}
