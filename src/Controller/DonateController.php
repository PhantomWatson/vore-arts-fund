<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Http\Exception\BadRequestException;

class DonateController extends AppController
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
            'index',
            'payment',
            'complete',
        ]);
    }

    public function index()
    {
        $title = 'Donate';
        $this->set(compact('title'));
    }

    public function payment()
    {
        $this->addControllerBreadcrumb();
        $request = $this->getRequest();
        $request->allowMethod(['post']);
        $amount = (int)$request->getData('amount') * 100; // In cents
        if (!$amount) {
            throw new BadRequestException('No amount provided');
        }
        $title = 'Payment info';
        $this->set(compact('title', 'amount'));
    }

    public function complete()
    {
        $this->addControllerBreadcrumb();
        $title = 'Donation complete';
        $this->set(compact('title'));
    }
}
