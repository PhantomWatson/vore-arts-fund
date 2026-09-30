<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Model\Entity\Article;
use App\Model\Entity\ArticleImage;
use Cake\Event\EventInterface;

/**
 * Articles Controller
 *
 * @property \App\Model\Table\ArticlesTable $Articles
 * @method \App\Model\Entity\Article[]|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Article> paginate(\Cake\Datasource\RepositoryInterface|\Cake\Datasource\QueryInterface|string|null $object = null, array $settings = [])
 */
class ArticlesController extends AdminController
{
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->addControllerBreadcrumb();
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
            ->contain(['Users'])
            ->orderByDesc('Articles.dated');
        $articles = $this->paginate($query);

        $this->set(compact('articles'));
        $this->title('Articles');
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $article = $this->Articles->newEmptyEntity();

        if ($this->request->is('post')) {
            $requestData = (array)$this->request->getData();
            $article = $this->Articles->patchEntity($article, $requestData);
            $article->title = trim($article->title);
            $article->user_id = $this->getAuthUser()->id;
            $article->slug = $article->generateUniqueSlug();
            if ($this->Articles->save($article)) {
                $this->processImages($requestData, $article);
                $this->Flash->success(__('The article has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The article could not be saved. Please try again.'));
        } else {
            $article->dated = date('Y-m-d');
            $article->is_published = true;
        }
        $this->set(compact('article'));
        $this->viewBuilder()->setTemplate('form');
        $this->title('Add article');
        $this->setRichTextEditorFilePaths();
        $this->set('toLoad', $this->getAppFiles('image-uploader/dist', 'image-uploader/dist/styles'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Article id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $article = $this->Articles->get($id, contain: ['Images']);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $requestData = (array)$this->request->getData();
            $article = $this->Articles->patchEntity($article, $requestData);
            if ($this->Articles->save($article)) {
                $this->processImages($requestData, $article);
                $this->Flash->success(__('The article has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The article could not be saved. Please, try again.'));
        }
        $users = $this->Articles->Users->find('list', ['limit' => 200])->all();
        $this->set(compact('article', 'users'));
        $this->viewBuilder()->setTemplate('form');
        $this->title('Edit article');
        $this->setRichTextEditorFilePaths();
        $this->set('toLoad', $this->getAppFiles('image-uploader/dist', 'image-uploader/dist/styles'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Article id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $article = $this->Articles->get($id);
        if ($this->Articles->delete($article)) {
            $this->Flash->success(__('The article has been deleted.'));
        } else {
            $this->Flash->error(__('The article could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * @param array $data Request data
     * @param \App\Model\Entity\Article $article
     * @return void
     */
    protected function processImages(array $data, Article $article): void
    {
        $imagesTable = $this->fetchTable('ArticleImages');
        foreach ($data['images'] ?? [] as $key => $data) {
            $weight = $key + 1;
            $filename = $data['filename'] ?? null;
            $caption = $data['caption'] ?? '';

            // Find or create image
            /** @var ArticleImage|null $image */
            $image = $imagesTable->getByFilename($filename);
            if ($image) {
                if ($image->article_id != $article->id) {
                    $this->Flash->error(
                        "The image $filename is not associated with article $article->id"
                        . $this->errorTryAgainContactMsg,
                        ['escape' => false],
                    );
                    continue;
                }
            } else {
                /** @var \App\Model\Entity\ArticleImage $image */
                $image = $imagesTable->newEmptyEntity();
                $image->article_id = $article->id;
                $image->filename = $filename;
            }

            // Set new weight and caption
            $image->weight = $weight;
            $image->caption = $caption;
            if (!$imagesTable->save($image)) {
                $this->Flash->error(
                    'There was an error saving an image. Details: Record could not be added to database. '
                    . $this->errorTryAgainContactMsg,
                    ['escape' => false],
                );
            }
        }
    }
}
