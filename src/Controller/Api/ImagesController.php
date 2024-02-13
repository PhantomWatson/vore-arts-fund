<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Model\Entity\Image;
use Cake\Http\Exception\ForbiddenException;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

/**
 * Images Controller
 *
 * @property \App\Model\Table\ImagesTable $Images
 * @method Image[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ImagesController extends ApiController
{
    public function remove(): void
    {
        $this->viewBuilder()->setClassName('Json');
        $this->getRequest()->allowMethod('delete');

        // Get image
        $filename = $this->getRequest()->getData('filename');
        $image = $this->Images->getByFilename($filename);
        if (!$image) {
            $this->setResponse($this->getResponse()->withStatus(204));
            return;
        }

        // Auth check
        $projectsTable = TableRegistry::getTableLocator()->get('Projects');
        $user = $this->getAuthUser();
        $isOwner = $projectsTable->exists([
            'id' => $image->project_id,
            'user_id' => $user->id,
        ]);
        if (!$isOwner) {
            throw new ForbiddenException();
        }

        // Delete image
        $this->Images->delete($image);

        $this->setResponse($this->getResponse()->withStatus(204));
    }
}
