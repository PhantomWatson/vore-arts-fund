<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Transaction;
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
        $user = $this->getAuthUser();
        $this->set(compact('title', 'user'));
    }

    public function payment()
    {
        $this->addControllerBreadcrumb();
        $request = $this->getRequest();

        // Bounce user back to index page if accessing with GET
        if (!$request->is('post')) {
            return $this->redirect(['action' => 'index']);
        }

        $donationAmount = (int)$request->getData('amount') * 100; // In cents
        if (!$donationAmount) {
            throw new BadRequestException('No amount provided');
        }

        // Cover Stripe's processing fee
        $coverProcessingFee = (bool)$request->getData('coverProcessingFee');
        $totalAmount = $coverProcessingFee
            ? ceil(($donationAmount + Transaction::STRIPE_FEE_FIXED)/(1 - Transaction::STRIPE_FEE_PERCENTAGE))
            : $donationAmount;

        $name = trim($request->getData('name'));
        $title = 'Payment info';
        $this->set(compact(
            'donationAmount',
            'name',
            'title',
            'totalAmount',
        ));

        return null;
    }

    public function complete()
    {
        $this->addControllerBreadcrumb();
        $title = 'Donation complete';
        $this->set(compact('title'));
    }
}
