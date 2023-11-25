<?php
declare(strict_types=1);

namespace App\Controller\API;

use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\InternalErrorException;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Log\Log;
use Stripe\StripeClient;

/**
 * @property \App\Model\Table\TransactionsTable $Transactions
 */
class TransactionsController extends ApiController
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
            'chargeSucceeded',
        ]);
    }

    /**
     * POST /api/transactions/charge-succeeded endpoint
     *
     * @return void
     * @throws BadRequestException
     * @throws ForbiddenException
     * @throws InternalErrorException
     * @throws MethodNotAllowedException
     */
    public function chargeSucceeded(): void
    {
        if (!$this->request->is(['post', 'options'])) {
            throw new MethodNotAllowedException('Only POST is supported at this endpoint. Method ' . $this->request->getMethod() . ' is not allowed.');
        }

        $stripe = new StripeClient(Configure::read('Stripe.secret_key'));

        // This is your Stripe CLI webhook secret for testing your endpoint locally.
        $endpointSecret = Configure::read('Stripe.webhook_signing_secret');

        $payload = @file_get_contents('php://input');
        $this->writeToStripeLog("Payload: $payload" ?: '(no payload)');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'];

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sigHeader, $endpointSecret
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            $this->writeToStripeLog('Invalid payload');
            exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            $this->writeToStripeLog('Invalid signature');
            exit();
        }

        // Handle the event
        $succeeded = false;
        switch ($event->type) {
            case 'charge.succeeded':
                $charge = $event->data->object;
                $this->writeToStripeLog('Charge: ' . print_r($charge, true));
                if (get_class($charge) == \Stripe\Charge::class) {
                    $succeeded = $this->Transactions->addPayment($charge);
                } else {
                    $this->writeToStripeLog('Cannot save charge: Not a \Stripe\Charge object', 'error');
                }
                break;
            default:
                $msg = 'Received unknown event type ' . $event->type;
                $this->writeToStripeLog($msg);
                echo $msg;
        }

        $this->set(['result' => $succeeded]);
        $this->viewBuilder()->setOption('serialize', ['result']);
        $this->viewBuilder()->setClassName('Json');
    }

    private function writeToStripeLog(string $message, $level = 'info'): void
    {
        Log::write($level, $message, ['scope' => 'stripe']);
    }
}
