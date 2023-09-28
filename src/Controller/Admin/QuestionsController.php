<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Model\Entity\Question;
use Cake\Datasource\ResultSetInterface;
use Cake\Event\EventInterface;

/**
 * Questions Controller
 *
 * @property \App\Model\Table\QuestionsTable $Questions
 * @method Question[]|ResultSetInterface paginate($object = null, array $settings = [])
 */
class QuestionsController extends AdminController
{
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->addControllerBreadcrumb('Application Questions');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $questions = [
            'enabled' => $this
                ->Questions
                ->find()
                ->where(['enabled' => true])
                ->orderAsc('weight')
                ->toArray(),
            'disabled' => $this
                ->Questions
                ->find()
                ->where(['enabled' => false])
                ->orderAsc('question')
                ->toArray()
        ];

        $this->set(compact('questions'));
        $this->title('Application Questions');
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $question = $this->Questions->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['weight'] = $this->Questions->getHighestWeight() + 1;
            $question = $this->Questions->patchEntity($question, $data);
            if ($this->Questions->save($question)) {
                $this->Flash->success(__('The question has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The question could not be saved. Please, try again.'));
        } else {
            $heaviestQuestion = $this
                ->Questions
                ->find('forProject')
                ->last();
            $question->weight = $heaviestQuestion ? $heaviestQuestion->weight + 1 : 0;
        }
        $this->set(compact('question'));
        $this->viewBuilder()->setTemplate('form');
        $this->title('Add Question');
    }

    /**
     * Edit method
     *
     * @param string|null $id Question id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $question = $this->Questions->get($id, [
            'contain' => [],
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $question = $this->Questions->patchEntity($question, $this->request->getData());
            if ($this->Questions->save($question)) {
                $this->Flash->success(__('The question has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The question could not be saved. Please, try again.'));
        }
        $this->set(compact('question'));
        $this->viewBuilder()->setTemplate('form');
        $this->title('Edit Question');
        if ($question->hasAnswers) {
            $this->Flash->warning('This question has received answers');
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id Question id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $question = $this->Questions->get($id);
        if ($this->Questions->delete($question)) {
            $this->Flash->success(__('The question has been deleted.'));
        } else {
            $this->Flash->error(__('The question could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
