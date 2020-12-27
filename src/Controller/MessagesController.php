<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Response;

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
     * @return \Cake\Http\Response|null
     */
    public function compose(): ?Response
    {
        $message = $this->Messages->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['user_id'] = $this->Auth->user('id');
            $message = $this->Messages->patchEntity($message, $data);
            if ($this->Messages->save($message)) {
                $this->Flash->success('Message successfully sent.');

                return $this->redirect(['action' => 'outbox']);
            }
        }
        $this->set(compact('message'));

        return null;
    }
}
