<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;
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
    const MAX_SLUG_LENGTH = 100;

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

    public function generateUniqueSlug()
    {
        $articlesTable = TableRegistry::getTableLocator()->get('Articles');
        $maxLength = self::MAX_SLUG_LENGTH;
        $slug = substr(Text::slug(strtolower($this->title)), 0, $maxLength);
        if (!$articlesTable->exists(['slug' => $slug])) {
            return $slug;
        }

        // Add date to make unique
        $date = $this->dated->format('Y-m-d');
        $maxLength = self::MAX_SLUG_LENGTH - strlen($date) - 1;
        $retval = substr($slug, 0, $maxLength) . '-' . $date;
        if (!$articlesTable->exists(['slug' => $retval])) {
            return $retval;
        }

        $i = 2;
        do {
            $maxLength = self::MAX_SLUG_LENGTH - strlen($date) - 2 - strlen("$i");
            $retval = substr($slug, 0, $maxLength) . "-$date-$i";
            $i++;
        } while ($articlesTable->exists(['slug' => $retval]));

        return $retval;
    }

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
