<?php
declare(strict_types=1);

namespace App\View\Helper;

use App\Model\Entity\Image;
use Cake\View\Helper;

/**
 * Image helper
 */
class ImageHelper extends Helper
{
    /**
     * Default configuration.
     *
     * @var array<string, mixed>
     */
    protected $_defaultConfig = [];

    /**
     * Shows a thumbnail that opens a full-sized image
     *
     * @param Image $image
     * @return string
     */
    public function thumb(Image $image): string
    {
        return sprintf(
            '<img src="/img/applications/%s%s" alt="%s" class="img-thumbnail" ' .
                'title="Click to open full-size image" data-full="/img/applications/%s" />',
            Image::THUMB_PREFIX,
            $image->filename,
            $image->caption,
            $image->filename,
        );
    }

    /**
     * @return string
     */
    public function initViewer(): string
    {
        return '<script src="/viewerjs/viewer.min.js"></script><script src="/js/image-viewer.js"></script>';
    }
}
