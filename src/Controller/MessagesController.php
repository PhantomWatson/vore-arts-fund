<?php
declare(strict_types=1);

namespace App\Controller;

class MessagesController extends AppController
{
    public function inbox()
    {
        $messages = $this->Message->find('all', [
            'conditions' => [
                'user_id' => $this->Auth->user('id'),
            ],
        ]);
    }

    public function outbox()
    {
        $messages = $this->Message->find('all', [
            'conditions' => [
                'user' => $this->Auth->user('id'),
            ],
        ]);
    }

    public function compose()
    {
        if ($this->request->is('post')) {
            $this->requrest->data['Message']['user_id'] = $this->Auth->user('id');
            if ($this->Message->save($this->request->data)) {
                $this->Session->setFlash('Message successfully sent.');
                $this->redirect(['action' => 'outbox']);
            }
        }
    }
}
