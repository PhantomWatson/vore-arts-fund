<?php
declare(strict_types=1);

namespace App\Controller\My;

use App\Controller\AppController;

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
            $bio = $this->Bios->patchEntity($bio, $this->request->getData(), ['fields' => ['bio', 'image', 'title']]);
            if ($this->Bios->save($bio)) {
                $this->Flash->success('Bio updated');

                return $this->redirect(['action' => 'edit']);
            }
            $this->Flash->error(__('Your bio could not be updated'));
        }
        $this->title('Update Bio');
    }
}
