<?php

namespace App;

use App\Model\Entity\Image;
use App\Model\Entity\User;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\InternalErrorException;
use Cake\Log\Log;
use Cake\Utility\Security;
use Cake\Utility\Text;

class ImageProcessor
{
    // Dimensions in pixels
    const MAX_HEIGHT_FULL = 2000;
    const MAX_WIDTH_FULL = 2000;
    const MAX_WIDTH_THUMB = 300;
    const MAX_HEIGHT_THUMB = 300;

    // Quality values are in the range of 1 to 100
    const QUALITY_THUMB = 90;
    const QUALITY_FULL = 95;

    public array $fileTypes = ['jpg', 'jpeg', 'gif', 'png', 'webp'];
    public string $extension;
    public string $filename;
    private string $sourceFilePath;

    /**
     * @param string[] $sourceFile
     * @return void
     * @throws InternalErrorException
     * @throws BadRequestException
     */
    public function processUpload(array $sourceFile): void
    {
        if (!is_file($sourceFile['tmp_name'])) {
            throw new InternalErrorException('No file was uploaded');
        }

        $this->sourceFilePath = $sourceFile['tmp_name'];
        $this->setExtension($sourceFile['name']);
        $this->filename = $this->generateRandomFilename($this->extension);

        // Resize and save thumbnail
        $destination = Image::PROJECT_IMAGES_DIR . DS . Image::THUMB_PREFIX . $this->filename;
        $this->resizeThumb($destination);

        // Resize and save fullsize image
        $destination = Image::PROJECT_IMAGES_DIR . DS . $this->filename;
        $this->resizeOriginal($destination);
    }

    public function generateRandomFilename(string $extension): string
    {
        return Security::randomString(10) . '.' . $extension;
    }

    /**
     * @param string[] $sourceFile
     * @return void
     * @throws InternalErrorException
     * @throws BadRequestException
     */
    public function processHeadshotUpload(array $sourceFile, User $user): void
    {
        if (!is_file($sourceFile['tmp_name'])) {
            throw new InternalErrorException('No file was uploaded');
        }

        $this->sourceFilePath = $sourceFile['tmp_name'];
        $this->setExtension($sourceFile['name']);
        $this->filename = substr(Text::slug($user->name), 0, 30) . '.' . $this->extension;

        // Resize and save thumbnail
        $dir = Image::BIO_HEADSHOTS_DIR . DS . $user->id;
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        $destination = $dir . DS . $this->filename;
        $this->resizeThumb($destination);
    }

    /**
     * Consumed as list($width, $height) = $this->getOriginalDimensions();
     *
     * @return array
     */
    private function getOriginalDimensions(): array
    {
        $retval = getimagesize($this->sourceFilePath);

        if (!$retval) {
            throw new BadRequestException('File is not a valid image: ' . $this->sourceFilePath);
        }

        return $retval;
    }

    /**
     * @param int $originalWidth
     * @param int $originalHeight
     * @param int|null $maxWidth
     * @param int|null $maxHeight
     * @return array|null[]|int[]
     */
    private function getScaledDimensions($originalWidth, $originalHeight, $maxWidth, $maxHeight): array
    {
        if ($originalWidth < $maxWidth && $originalHeight < $maxHeight) {
            return [$originalWidth, $originalHeight];
        }

        // Make the longest side fit inside the maximum dimensions
        $newWidth = $originalWidth >= $originalHeight ? $maxWidth : null;
        $newHeight = $originalWidth >= $originalHeight ? null : $maxHeight;

        return [$newWidth, $newHeight];
    }

    /**
     * Resizes the full-size image to keep it inside of maximum dimensions
     *
     * @param $destination
     * @return void
     */
    private function resizeOriginal($destination): void
    {
        list($width, $height) = $this->getOriginalDimensions();
        [$newWidth, $newHeight] = $this->getScaledDimensions(
            $width,
            $height,
            self::MAX_WIDTH_FULL,
            self::MAX_HEIGHT_FULL
        );
        $this->makeResizedCopy($destination, $newWidth, $newHeight, self::QUALITY_FULL);
    }

    private function resizeThumb($destination): void
    {
        list($width, $height) = $this->getOriginalDimensions();
        [$newWidth, $newHeight] = $this->getScaledDimensions(
            $width,
            $height,
            self::MAX_WIDTH_THUMB,
            self::MAX_HEIGHT_THUMB
        );
        $this->makeResizedCopy($destination, $newWidth, $newHeight, self::QUALITY_THUMB);
    }

    /**
     * Resizes an image to the provided dimensions, saving results in $newFilename
     *
     * @param string $outputFile Full path to filename for output
     * @param int|null $newWidth Width in pixels, or NULL to scale automatically
     * @param int|null $newHeight Height in pixels, or NULL to scale automatically
     * @param int $quality Quality (1-100) of saved image
     * @return void
     * @throws InternalErrorException
     */
    public function makeResizedCopy(string $outputFile, ?int $newWidth, ?int $newHeight, int $quality = 100): void
    {
        list($originalWidth, $originalHeight) = $this->getOriginalDimensions();

        if (!$newWidth && !$newHeight) {
            $newWidth = $originalWidth;
            $newHeight = $originalHeight;
        } else {
            $newWidth = $newWidth ?? floor($newHeight * ($originalWidth / $originalHeight));
            $newHeight = $newHeight ?? floor($newWidth * $originalHeight / $originalWidth);
        }

        match ($this->extension) {
            'gif' => $this->resizeGif($outputFile, $newWidth, $newHeight),
            'png' => $this->resizePng($outputFile, $newWidth, $newHeight, $quality),
            'webp' => $this->resizeWebp($outputFile, $newWidth, $newHeight, $quality),
            default => $this->resizeJpeg($outputFile, $newWidth, $newHeight, $quality),
        };
    }

    /**
     * Resizes a GIF format image
     *
     * @param string $outputFile outgoing
     * @param int $scaledWidth width
     * @param int $scaledHeight height
     * @return void
     * @throws InternalErrorException
     */
    private function resizeGif($outputFile, $scaledWidth, $scaledHeight): void
    {
        $sourceImage = $this->getGdSourceImage();
        $tmpImage = $this->getTmpDestinationImage($scaledWidth, $scaledHeight);
        $this->resizeImg($tmpImage, $sourceImage, $scaledWidth, $scaledHeight);

        $saveResult = imagegif($tmpImage, $outputFile);
        if (!$saveResult) {
            throw new InternalErrorException('There was an error with your image (imagegif() failed)');
        }

        imagedestroy($tmpImage);
    }

    /**
     * Resizes a JPEG format image
     *
     * @param string $outputFile outgoing
     * @param int $scaledWidth width
     * @param int $scaledHeight height
     * @param int $quality of image
     * @return void
     * @throws InternalErrorException
     */
    private function resizeJpeg($outputFile, $scaledWidth, $scaledHeight, $quality): void
    {
        $sourceImage = $this->getGdSourceImage();
        $tmpImage = $this->getTmpDestinationImage($scaledWidth, $scaledHeight);
        $this->resizeImg($tmpImage, $sourceImage, $scaledWidth, $scaledHeight);

        $saveResult = imagejpeg($tmpImage, $outputFile, $quality);
        if (!$saveResult) {
            throw new InternalErrorException('There was an error with your image (imagejpeg() failed)');
        }

        imagedestroy($tmpImage);
    }

    /**
     * Resizes a PNG format image
     *
     * @param string $outputFile outgoing
     * @param int $scaledWidth width
     * @param int $scaledHeight height
     * @param int $quality of image
     * @return void
     * @throws InternalErrorException
     */
    private function resizePng($outputFile, $scaledWidth, $scaledHeight, $quality): void
    {
        $sourceImage = $this->getGdSourceImage();
        $tmpImage = $this->getTmpDestinationImage($scaledWidth, $scaledHeight);
        imagealphablending($tmpImage, false);
        $this->resizeImg($tmpImage, $sourceImage, $scaledWidth, $scaledHeight);

        imagesavealpha($tmpImage, true);
        $compression = $this->getPngCompression($quality);
        $saveResult = imagepng($tmpImage, $outputFile, $compression);
        if (!$saveResult) {
            throw new InternalErrorException('There was an error with your image (imagepng() failed)');
        }

        imagedestroy($tmpImage);
    }

    /**
     * Resizes a GIF format image
     *
     * @param string $outputFile outgoing
     * @param int $scaledWidth width
     * @param int $scaledHeight height
     * @param int $quality
     * @return void
     * @throws InternalErrorException
     */
    private function resizeWebp($outputFile, $scaledWidth, $scaledHeight, $quality): void
    {
        $sourceImage = $this->getGdSourceImage();
        $tmpImage = $this->getTmpDestinationImage($scaledWidth, $scaledHeight);
        $this->resizeImg($tmpImage, $sourceImage, $scaledWidth, $scaledHeight);

        $saveResult = imagewebp($tmpImage, $outputFile, $quality);
        if (!$saveResult) {
            throw new InternalErrorException('There was an error with your image (imagegif() failed)');
        }

        imagedestroy($tmpImage);
    }

    /**
     * @return \GdImage
     * @throws InternalErrorException
     */
    private function getGdSourceImage(): \GdImage
    {
        $image = match ($this->extension) {
            'jpg', 'jpeg' => imagecreatefromjpeg($this->sourceFilePath),
            'png' => imagecreatefrompng($this->sourceFilePath),
            'gif' => imagecreatefromgif($this->sourceFilePath),
            'webp' => imagecreatefromwebp($this->sourceFilePath),
            default => null
        };
        if (!$image) {
            throw new InternalErrorException('There was an error resizing your image (getting source image failed)');
        }

        return $image;
    }

    /**
     * @param int|null $scaledWidth
     * @param int|null $scaledHeight
     * @return \GdImage
     */
    private function getTmpDestinationImage($scaledWidth, $scaledHeight): \GdImage
    {
        $tmpImage = imagecreatetruecolor($scaledWidth, $scaledHeight);
        if (!$tmpImage) {
            throw new InternalErrorException('There was an error resizing your image (tmp destination image failed)');
        }

        return $tmpImage;
    }

    /**
     * @param \GdImage $tmpImage
     * @param \GdImage $sourceImage
     * @param int|null $scaledWidth
     * @param int|null $scaledHeight
     * @return void
     * @throws InternalErrorException
     */
    private function resizeImg($tmpImage, $sourceImage, $scaledWidth, $scaledHeight): void
    {
        list($width, $height) = getimagesize($this->sourceFilePath);
        $resizeResult = imagecopyresampled(
            $tmpImage,
            $sourceImage,
            0,
            0,
            0,
            0,
            $scaledWidth,
            $scaledHeight,
            $width,
            $height
        );
        if (!$resizeResult) {
            throw new InternalErrorException('There was an error with your image (imagecopyresampled() failed)');
        }
    }

    /**
     * Converts a quality value (0 to 100) to a PNG compression value (9 to 0)
     *
     * PNG compression values work inversely to quality, as larger compression values correspond to lower quality
     *
     * @param int $quality Quality value from 0 to 100
     * @return int
     * @throws InternalErrorException
     */
    private function getPngCompression(int $quality): int
    {
        if ($quality < 0 || $quality > 100) {
            throw new InternalErrorException('Image quality is out of range (' . $quality . ')');
        }

        $result = 10 - floor($quality / 10);

        return (int)min(9, $result);
    }

    /**
     * Sets the extension property of this image, based on the original image filename
     *
     * @param string $sourceFileName
     * @return void
     * @throws BadRequestException
     */
    public function setExtension($sourceFileName): void
    {
        $this->extension = $this->extractExtension($sourceFileName);

        if (!in_array($this->extension, $this->fileTypes)) {
            throw new BadRequestException('Invalid file type (only JPG, GIF, PNG, and WEBP are allowed)');
        }
    }

    public function extractExtension(string $filename): string
    {
        $filenameParts = explode('.', $filename);
        return mb_strtolower(end($filenameParts));
    }

    /**
     * Makes a copy of an image file, including its thumbnail, and returns the new filename
     *
     * @param string $oldFilename
     * @return string
     */
    public function makeCopy(string $oldFilename): string
    {
        $extension = $this->extractExtension($oldFilename);
        $newFilename = $this->generateRandomFilename($extension);

        // Copy thumbnail
        $sourceFile = Image::PROJECT_IMAGES_DIR . DS . Image::THUMB_PREFIX . $oldFilename;
        $destinationFile = Image::PROJECT_IMAGES_DIR . DS . Image::THUMB_PREFIX . $newFilename;
        if (!copy($sourceFile, $destinationFile)) {
            Log::error('Failed to copy file from ' . $sourceFile . ' to ' . $destinationFile);
            throw new InternalErrorException('Failed to copy file');
        }

        // Copy fullsize image
        $sourceFile = Image::PROJECT_IMAGES_DIR . DS . $oldFilename;
        $destinationFile = Image::PROJECT_IMAGES_DIR . DS . $newFilename;
        if (!copy($sourceFile, $destinationFile)) {
            Log::error('Failed to copy file from ' . $sourceFile . ' to ' . $destinationFile);
            throw new InternalErrorException('Failed to copy file');
        }

        return $newFilename;
    }
}
