<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

/**
 * Articles Controller
 *
 * @property \App\Model\Table\ArticlesTable $Articles
 * @method \App\Model\Entity\Article[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ArticlesController extends AppController
{
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->addBreadcrumb(
            'News',
            [
                'controller' => 'Articles',
                'action' => 'index'
            ]
        );
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Articles
            ->find()
            ->where(['is_published' => true])
            ->order(['Articles.dated' => 'DESC']);
        $articles = $this->paginate($query);

        $this->set(compact('articles'));
        $this->title('News');
    }

    /**
     * View method
     *
     * @param string|null $id Article id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view()
    {
        $slug = $this->request->getParam('slug');
        $article = $this->Articles
            ->find()
            ->where(['slug' => $slug, 'is_published' => true])
            ->firstOrFail();

        $this->title($article->title);

        $this->addBreadcrumb($article->title, []);

        $this->set(compact('article'));
    }
}
