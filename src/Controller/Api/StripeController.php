<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Model\Entity\Transaction;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\InternalErrorException;
use Cake\Http\Exception\MethodNotAllowedException;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\Stripe;

/**
 * @property \App\Model\Table\VotesTable $Votes
 */
class StripeController extends ApiController
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
            'createPaymentIntent',
        ]);
    }

    /**
     * Used by checkout.js
     *
     * @return void
     * @throws BadRequestException
     * @throws ForbiddenException
     * @throws InternalErrorException
     * @throws MethodNotAllowedException
     */
    public function createPaymentIntent()
    {
        if (!$this->getRequest()->is(['post', 'options'])) {
            throw new MethodNotAllowedException(
                'Only POST is supported at this endpoint. Method ' . $this->request->getMethod() . ' is not allowed.'
            );
        }

        $amount = $this->getRequest()->getData('amount');
        if (!$amount) {
            throw new BadRequestException('No amount provided');
        }

        $transactionType = $this->getRequest()->getData('transactionType');
        if (!$transactionType || !in_array($transactionType, [
            Transaction::TYPE_DONATION,
            Transaction::TYPE_LOAN_REPAYMENT,
        ])) {
            throw new BadRequestException('Invalid transaction type');
        }

        $payerName = $this->getRequest()->getData('payerName');
        $description = ($transactionType == Transaction::TYPE_DONATION ? 'Donation' : 'Loan repayment')
            . ($payerName ? " from $payerName" : ' (anonymous)');

        $metadata = [
            'name' => $payerName,
            'transactionType' => $transactionType,
        ];
        if ($this->getRequest()->getData('projectId')) {
            $metadata['projectId'] = $this->getRequest()->getData('projectId');
        }

        $stripeSecretKey = Configure::read('Stripe.secret_key');
        Stripe::setApiKey($stripeSecretKey);
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'usd',
                'automatic_payment_methods' => ['enabled' => true],
                'description' => $description,
                'metadata' => $metadata
            ]);
            $result = ['clientSecret' => $paymentIntent->client_secret];
        } catch (ApiErrorException $e) {
            http_response_code(500);
            $result = $e->getMessage();
        }

        $this->set(compact('result'));
        $this->viewBuilder()->setOption('serialize', ['result']);
        $this->viewBuilder()->setClassName('Json');
    }
}
