<?php
declare(strict_types=1);

namespace App\Controller\Api;

use Cake\Event\EventInterface;

/**
 * Error Handling Controller
 *
 * Controller used by ExceptionRenderer to render error responses.
 */
class ErrorController extends \Cake\Controller\ErrorController
{
    /**
     * beforeRender callback.
     *
     * @param \Cake\Event\EventInterface $event Event.
     * @return \Cake\Http\Response|null|void
     */
    public function beforeRender(EventInterface $event)
    {
        parent::beforeRender($event);
        if ($this->getRequest()->getParam('prefix') == 'Api') {
            $this->viewBuilder()->setClassName('Json');
        }
    }
}
