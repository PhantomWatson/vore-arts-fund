<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\View\Exception\MissingTemplateException;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 *
 * @property \App\Model\Table\FundingCyclesTable $FundingCycles
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 */
class PagesController extends AppController
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
            'display',
            'about',
            'contact',
            'terms',
            'privacy',
            'home',
        ]);
    }

    /**
     * Displays a view
     *
     * @param string ...$path Path segments.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Http\Exception\ForbiddenException When a directory traversal attempt.
     * @throws \Cake\View\Exception\MissingTemplateException When the view file could not
     *   be found and in debug mode.
     * @throws \Cake\Http\Exception\NotFoundException When the view file could not
     *   be found and not in debug mode.
     * @throws \Cake\View\Exception\MissingTemplateException In debug mode.
     */
    public function display(string ...$path): ?Response
    {
        if (!$path) {
            return $this->redirect('/');
        }
        if (in_array('..', $path, true) || in_array('.', $path, true)) {
            throw new ForbiddenException();
        }
        $page = $subpage = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        $this->set(compact('page', 'subpage'));

        try {
            return $this->render(implode('/', $path));
        } catch (MissingTemplateException $exception) {
            if (Configure::read('debug')) {
                throw $exception;
            }
            throw new NotFoundException();
        }
    }

    /**
     * "About" page
     *
     * @return void
     */
    public function about()
    {
        $this->title('About the Vore Arts Fund');
        $this->setCurrentBreadcrumb('About');
    }

    /**
     * Contact information page
     *
     * @return void
     */
    public function contact()
    {
        $this->title('Contact');
    }

    /**
     * Terms of use page
     *
     * @return void
     */
    public function terms()
    {
        $this->title('Terms');
    }

    /**
     * Privacy policy page
     *
     * @return void
     */
    public function privacy()
    {
        $this->title('Privacy Policy');
    }

    /**
     * @return void
     */
    public function home()
    {
        $fundingCyclesTable = $this->fetchTable('FundingCycles');
        /** @var \App\Model\Entity\FundingCycle $fundingCycle */
        $fundingCycle = $fundingCyclesTable
            ->find('currentAndFuture')
            ->orderAsc('application_end')
            ->first();
        $fundingCycleIsCurrent = $fundingCycle && $fundingCycle->application_begin->isPast();
        $this->set([
            'fundingCycle' => $fundingCycle,
            'fundingCycleIsCurrent' => $fundingCycleIsCurrent,
            'title' => '',
        ]);
    }

    public function maintenanceMode(): void
    {
        $this->title('Hang tight! We\'re undergoing maintenance.');
    }
}
