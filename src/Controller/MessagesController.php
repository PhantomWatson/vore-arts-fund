<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Class MessagesController
 *
 * @package App\Controller
 * @property \App\Model\Table\MessagesTable $Messages
 */
class MessagesController extends AppController
{
    /**
     * Messages inbox
     *
     * @return void
     */
    public function inbox()
    {
        $messages = $this->Messages
            ->find()
            ->where(['user_id' => $this->Auth->user('id')]);
        $this->set(compact('messages'));
    }

    /**
     * Messages outbox
     *
     * @return void
     */
    public function outbox()
    {
        $messages = $this->Messages
            ->find()
            ->where(['user' => $this->Auth->user('id')]);
        $this->set(compact('messages'));
    }

    /**
     * Page for writing a message
     *
     * @return void
     */
    public function compose()
    {
        $message = $this->Messages->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['user_id'] = $this->Auth->user('id');
            $message = $this->Messages->patchEntity($message, $data);
            if ($this->Messages->save($message)) {
                $this->Session->setFlash('Message successfully sent.');
                $this->redirect(['action' => 'outbox']);
            }
        }
        $this->set(compact('message'));
    }
}
