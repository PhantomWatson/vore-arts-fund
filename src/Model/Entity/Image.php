<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Image Entity
 *
 * @property int $id
 * @property string|null $filename
 * @property int $weight
 * @property string|null $caption
 * @property \Cake\I18n\DateTime $created
 */
abstract class Image extends Entity
{
    public const THUMB_PREFIX = 'thumb_';
    public const PROJECT_IMAGES_DIR = WWW_ROOT . 'img' . DS . 'projects';

    public const BIO_HEADSHOTS_DIR = WWW_ROOT . 'img' . DS . 'bios';
}
