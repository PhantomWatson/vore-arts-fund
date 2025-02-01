<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Http\Response;

/**
 * MailingList Controller
 *
 * @method \App\Model\Entity\MailingList[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MailingListController extends AppController
{
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated([
            'index',
            'signup',
            'thanks',
        ]);
        $this->addControllerBreadcrumb('Mailing List');
    }

    /**
     * Index redirect to signup
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->redirect(['action' => 'signup']);
    }

    /**
     * Signup
     *
     * @return void
     */
    public function signup()
    {
        // MailChimp signup form
        return $this->redirect('http://eepurl.com/i8XaZA');
        /*
        $title = 'Sign up for the mailing list';
        $this->set(compact('title'));
        $this->setCurrentBreadcrumb('Sign up');
        */
    }

    /**
     * "Thanks for signing up" page
     *
     * @return void
     */
    public function thanks(): void
    {
        $title = 'Thanks for subscribing!';
        $this->set(compact('title'));
        $this->setCurrentBreadcrumb('Thanks');
    }
}
