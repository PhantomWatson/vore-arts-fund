<?php
declare(strict_types=1);

namespace App\Controller\My;

use App\Controller\AppController;
use App\ImageProcessor;

/**
 * Bios Controller
 *
 * @property \App\Model\Table\BiosTable $Bios
 * @method \App\Model\Entity\Bio[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class BiosController extends AppController
{
    /**
     * Edit method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     */
    public function edit()
    {
        $user = $this->getAuthUser();
        if (!$user->is_admin) {
            $this->Flash->error('Bios are only enabled for admin users');
            return $this->redirect(['prefix' => false, 'controller' => 'Pages', 'action' => 'home']);
        }

        $bio = $this->Bios->getForUser($user->id);

        $this->set(compact('bio'));

        if (!$this->request->is(['get'])) {
            $data = $this->request->getData();
            $bio = $this->Bios->patchEntity($bio, $data, ['fields' => ['bio', 'title']]);
            if ($this->Bios->save($bio)) {
                $this->Flash->success('Bio updated');

                $file = $_FILES['image-file'] ?? null;
                if ($file['tmp_name']) {
                    $imageProcessor = new ImageProcessor();
                    $imageProcessor->processHeadshotUpload($file, $user);
                    $bio->image = $imageProcessor->filename;
                    if (!$this->Bios->save($bio)) {
                        $this->Flash->error('But there as an error uploading your headshot');
                    }
                }

                return $this->redirect(['action' => 'edit']);
            }
            $this->Flash->error('Your bio could not be updated');
        }
        $this->title('Update Bio');
    }
}
