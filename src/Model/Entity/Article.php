<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\View\Helper\TextHelper;
use Cake\View\View;

/**
 * Article Entity
 *
 * @property int $id
 * @property string $title
 * @property string $body
 * @property string $slug
 * @property int $user_id
 * @property bool $is_published
 * @property \Cake\I18n\FrozenDate|null $dated
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $formatted_body
 *
 * @property \App\Model\Entity\User $user
 */
class Article extends Entity
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
        'title' => true,
        'body' => true,
        'slug' => false,
        'user_id' => false,
        'is_published' => true,
        'dated' => true,
        'created' => false,
        'modified' => false,
        'user' => true,
    ];

    /**
     * Returns the article body with added line/paragraph breaks + linked URLs and email addresses
     *
     * @return string
     */
    protected function _getFormattedBody(): string
    {
        $textHelper = new TextHelper(new View());
        $body = $this->body;
        $body = $textHelper->autoParagraph($body);
        return $textHelper->autoLink($body, ['escape' => false]);
    }
}
