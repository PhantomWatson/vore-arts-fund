<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Image;
use Cake\Utility\Security;

/**
 * Images Controller
 *
 * @property \App\Model\Table\ImagesTable $Images
 * @method \App\Model\Entity\Image[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ImagesController extends AppController
{
    /**
     * Endpoint for uploading image files and returning their temporary path (does not create image database records)
     *
     * @return void
     */
    public function upload()
    {
        $images = [];

        $baseFilename = Security::randomString(10) . '.png';
        foreach ($_FILES as $key => $image) {
            $image = $_FILES[$key];
            $filename = $key == 'thumb' ? Image::THUMB_PREFIX . $baseFilename : $baseFilename;
            $destination = WWW_ROOT . 'img' . DS . 'projects' . DS . $filename;
            $images[$key] = move_uploaded_file($image['tmp_name'], $destination)
                ? $filename
                : false;
        }
        $this->set(compact('images'));
        $this->viewBuilder()->setLayout('ajax');
    }
}
