<?php
declare(strict_types=1);

namespace App\View\Helper;

use App\ImageProcessor;
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
    protected array $_defaultConfig = [];

    /**
     * Shows a thumbnail that opens a full-sized image
     *
     * @param Image $image
     * @param string $imageTable
     * @return string
     */
    public function thumb(Image $image, string $imageTable): string
    {
        if (!in_array($imageTable, ImageProcessor::VALID_IMAGE_TABLES)) {
            throw new \InvalidArgumentException('Invalid image table: ' . $imageTable);
        }

        return sprintf(
            '<img src="/img/%s/%s%s" alt="%s" class="img-thumbnail" ' .
                'title="Click to open full-size image" data-full="/img/%s/%s" />',
            $imageTable,
            Image::THUMB_PREFIX,
            $image->filename,
            $image->caption,
            $imageTable,
            $image->filename,
        );
    }
}
