<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\ImageProcessor;
use App\Model\Entity\Image;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\InternalErrorException;
use Cake\ORM\TableRegistry;

/**
 * Images Controller
 *
 * @property \App\Model\Table\ImagesTable $Images
 * @method Image[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ImagesController extends ApiController
{
    /**
     * Endpoint for uploading image files and returning their temporary path (does not create image database records)
     *
     * @return void
     * @throws BadRequestException
     * @throws InternalErrorException
     */
    public function upload()
    {
        $this->viewBuilder()
            ->setClassName('Json')
            ->setOption('jsonOptions', JSON_FORCE_OBJECT);

        $file = $_FILES['file'] ?? null;
        if (!$file) {
            throw new BadRequestException();
        }

        $imageProcessor = new ImageProcessor();
        $imageProcessor->processUpload($_FILES['file']);
        $filename = $imageProcessor->filename;

        $this->set(compact('filename'));
        $this->viewBuilder()->setOption('serialize', ['filename']);
        $this->setResponse($this->getResponse()->withStatus(201));
    }

    /**
     * @return void
     * @throws ForbiddenException
     * @throws InternalErrorException
     */
    public function remove(): void
    {
        $this->viewBuilder()->setClassName('Json');
        $this->getRequest()->allowMethod('delete');

        // Get image
        $filename = $this->getRequest()->getData('filename');
        $image = $this->Images->getByFilename($filename);

        if ($image) {
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
            if (!$this->Images->delete($image)) {
                throw new InternalErrorException();
            }

        // Image was uploaded but has no DB record because the project form had never been submitted
        } else {
            $this->Images->deleteImageFiles($filename);
        }

        $this->setResponse($this->getResponse()->withStatus(204));
        $this->set(['result' => true]);
        $this->viewBuilder()->setOption('serialize', ['result']);
    }
}
