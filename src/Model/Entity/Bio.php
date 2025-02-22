<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\View\Helper\TextHelper;
use Cake\View\View;

/**
 * Bio Entity
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $bio
 * @property string $formatted_bio
 * @property string|false $image_url
 * @property string|null $image
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\User $user
 */
class Bio extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected $_accessible = [
        'user_id' => true,
        'bio' => true,
        'image' => true,
        'created' => true,
        'modified' => true,
        'user' => true,
    ];

    /**
     * Returns the bio with added line/paragraph breaks + linked URLs and email addresses
     *
     * @return string
     */
    protected function _getFormattedBio(): string
    {
        $textHelper = new TextHelper(new View());
        $bio = $this->bio;
        $bio = $textHelper->autoParagraph($bio);
        return $textHelper->autoLink($bio, ['escape' => false]);
    }

    protected function _getImageUrl(): string|false
    {
        if ($this->image) {
            return '/img/bios/' . $this->user_id . '/' . $this->image;
        }
        return false;
    }
}
