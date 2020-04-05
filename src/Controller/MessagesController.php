<?php
class MessagesController extends AppController {
    public function inbox(){
        $messages = $this->Message->find('all', array(
            'conditions' => array(
                'user_id'=> $this->Auth->user('id')
            )
        ));
    }

    public function outbox(){
        $messages = $this->Message->find('all', array(
            'conditions'=> array(
                'user'=>$this->Auth->user('id')
            )
        ));
    }

    public function compose(){
        if($this->request->is('post')){
            $this->requrest->data['Message']['user_id'] = $this->Auth->user('id');
            if($this->Message->save($this->request->data)){
                $this->Session->setFlash('Message successfully sent.');
                $this->redirect(array('action'=>'outbox'));
            }
        }
    }


}