<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\ImageProcessor;
use App\Model\Table\ImagesTable;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\InternalErrorException;
use Cake\ORM\TableRegistry;

/**
 * Images Controller
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
    public function upload(string $imageTable): void
    {
        if (!in_array($imageTable, ImageProcessor::VALID_IMAGE_TABLES)) {
            throw new BadRequestException("Invalid image table: $imageTable");
        }

        $this->viewBuilder()
            ->setClassName('Json')
            ->setOption('jsonOptions', JSON_FORCE_OBJECT);

        $file = $_FILES['file'] ?? null;
        if (!$file) {
            throw new BadRequestException();
        }

        $imageProcessor = new ImageProcessor($imageTable);
        $imageProcessor->processUpload($_FILES['file']);
        $filename = $imageProcessor->filename;

        $this->set(compact('filename'));
        $this->viewBuilder()->setOption('serialize', ['filename']);
        $this->setResponse($this->getResponse()->withStatus(201));
    }

    /**
     * Returns the child class of ImagesTable corresponding to the given image table name
     *
     * @param string $imageTable The shorthand name of the image table
     * @return ImagesTable
     * @throws BadRequestException if the image table name is invalid
     */
    private function getImagesTable(string $imageTable): ImagesTable
    {
        return match ($imageTable) {
            'projects' => TableRegistry::getTableLocator()->get('ProjectImages'),
            'reports' => TableRegistry::getTableLocator()->get('ReportImages'),
            'articles' => TableRegistry::getTableLocator()->get('ArticleImages'),
            default => throw new BadRequestException("Invalid image table: $imageTable"),
        };
    }

    /**
     * @return void
     * @throws ForbiddenException
     * @throws InternalErrorException
     */
    public function remove(string $imageTable): void
    {
        $this->viewBuilder()->setClassName('Json');
        $this->getRequest()->allowMethod('delete');

        // Get image
        $filename = $this->getRequest()->getData('filename');
        $imagesTable = $this->getImagesTable($imageTable);
        $image = $imagesTable->getByFilename($filename);

        if ($image) {
            $user = $this->getAuthUser();

            if (!$imagesTable->isOwnedBy($image->id, $user->id)) {
                throw new ForbiddenException();
            }

            // Delete image
            if (!$imagesTable->delete($image)) {
                throw new InternalErrorException();
            }

        // Image was uploaded but has no DB record because the respective form had never been submitted
        } else {
            $imagesTable->deleteImageFiles($filename);
        }

        $this->setResponse($this->getResponse()->withStatus(204));
        $this->set(['result' => true]);
        $this->viewBuilder()->setOption('serialize', ['result']);
    }
}
