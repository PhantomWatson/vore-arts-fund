<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\Admin\FundingCyclesController;
use App\Event\AlertListener;
use App\Model\Entity\Transaction;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\Event\EventManager;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\InternalErrorException;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Log\Log;

/**
 * @property \App\Model\Table\TransactionsTable $Transactions
 */
class TransactionsController extends ApiController
{
    const ACCESSIBLE_FIELDS = ['date', 'type', 'name', 'amount_net', 'amount_gross', 'meta', 'project_id'];

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
        $this->viewBuilder()->setClassName('Json');
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
            throw new MethodNotAllowedException(
                'Only POST is supported at this endpoint. Method ' . $this->request->getMethod() . ' is not allowed.'
            );
        }

        // This is your Stripe CLI webhook secret for testing your endpoint locally.
        $endpointSecret = Configure::read('Stripe.webhook_signing_secret');

        $payload = @file_get_contents('php://input');
        $this->writeToStripeLog("Payload: $payload" ?: '(no payload)');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'];

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            $this->writeToStripeLog('Invalid payload');
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
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
                    $transaction = $this->Transactions->addPayment($charge);
                    $succeeded = $transaction !== false;
                } else {
                    $this->writeToStripeLog('Cannot save charge: Not a \Stripe\Charge object', 'error');
                }
                $this->dispatchChargeSucceededEvent((string)$payload, $transaction ?? null);
                break;
            default:
                $msg = 'Received unknown event type ' . $event->type;
                $this->writeToStripeLog($msg);
                echo $msg;
        }

        $this->set(['result' => $succeeded]);
        $this->viewBuilder()->setOption('serialize', ['result']);
    }

    private function writeToStripeLog(string $message, $level = 'info'): void
    {
        Log::write($level, $message, ['scope' => 'stripe']);
    }

    private function dispatchChargeSucceededEvent(string $payload, ?Transaction $transaction)
    {
        EventManager::instance()->on(new AlertListener());

        EventManager::instance()->dispatch(new Event(
            'Stripe.chargeSucceeded',
            $this,
            compact('payload', 'transaction')
        ));
    }

    /**
     * Throws an exception if the current user is not an admin
     *
     * @return void
     */
    private function restrictToAdmins(): void
    {
        $user = $this->getAuthUser();
        if (!$user) {
            throw new UnauthorizedException();
        }
        if (!($user->is_admin ?? false)) {
            throw new ForbiddenException();
        }
    }

    private function massageData($data)
    {
        // Convert dollars to cents
        $data['amount_gross'] *= 100;
        $data['amount_net'] *= 100;

        $data['date'] = FundingCyclesController::convertTimeToUtc($data['date']);

        return $data;
    }

    public function add(): void
    {
        $this->response = $this->getResponse()->cors($this->request)
            ->allowOrigin(['http://localhost:3000'])
            ->allowMethods(['POST'])
            ->build();
        $this->getRequest()->allowMethod('POST');
        $this->restrictToAdmins();
        $data = $this->massageData($this->getRequest()->getData());

        $transaction = $this->Transactions->newEntity(
            $data,
            ['fields' => self::ACCESSIBLE_FIELDS]
        );
        $transaction->user_id = $this->getAuthUser()->id;
        if ($this->Transactions->save($transaction)) {
            $this->response = $this->getResponse()->withStatus(201);
            $this->set(['success' => true]);
            $this->viewBuilder()->setOption('serialize', 'success');
            $this->Flash->success('Transaction added');
            return;
        }

        // Error
        $this->response = $this->getResponse()->withStatus(422);
        $this->set(['error' => $this->getEntityErrorDetails($transaction)]);
        $this->viewBuilder()->setOption('serialize', ['error']);
    }

    public function edit(): void
    {
        // This path allows PATCH and DELETE, but this method is just for PATCH
        $this->response = $this->getResponse()->cors($this->request)
            ->allowOrigin(['http://localhost:3000'])
            ->allowMethods(['PATCH', 'DELETE'])
            ->build();
        $this->getRequest()->allowMethod('PATCH');

        $this->restrictToAdmins();

        $id = $this->request->getParam('id');
        $transaction = $this->Transactions->get($id);
        $data = $this->massageData($this->getRequest()->getData());

        $transaction = $this->Transactions->patchEntity(
            $transaction,
            $data,
            ['fields' => self::ACCESSIBLE_FIELDS]
        );
        if ($this->Transactions->save($transaction)) {
            $this->set(['success' => true]);
            $this->viewBuilder()->setOption('serialize', 'success');
            $this->Flash->success('Transaction updated');
            return;
        }

        // Error
        $this->response = $this->getResponse()->withStatus(422);
        $this->set(['error' => $this->getEntityErrorDetails($transaction)]);
        $this->viewBuilder()->setOption('serialize', ['error']);
    }

    public function delete(): void
    {
        // This path allows PATCH and DELETE, but this method is just for DELETE
        $this->response = $this->getResponse()->cors($this->request)
            ->allowOrigin(['http://localhost:3000'])
            ->allowMethods(['PATCH', 'DELETE'])
            ->build();
        $this->getRequest()->allowMethod('DELETE');

        $this->restrictToAdmins();

        $id = $this->request->getParam('id');
        $transaction = $this->Transactions->get($id);
        if ($this->Transactions->delete($transaction)) {
            $this->set(['success' => true]);
            $this->viewBuilder()->setOption('serialize', 'success');
            $this->Flash->success('Transaction removed');
            return;
        }

        // Error
        $this->response = $this->getResponse()->withStatus(400);
        $this->set(['error' => $this->getEntityErrorDetails($transaction)]);
        $this->viewBuilder()->setOption('serialize', ['error']);
    }
}
