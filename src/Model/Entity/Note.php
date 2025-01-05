<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Note Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int $project_id
 * @property string $body
 * @property int $type
 * @property string $typeWithIcon
 * @property \Cake\I18n\FrozenTime $created
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Project $project
 */
class Note extends Entity
{
    public const TYPE_NOTE = 1;
    public const TYPE_MESSAGE_TO_APPLICANT = 2;
    public const TYPE_MESSAGE_FROM_APPLICANT = 3;
    public const TYPE_REVISION_REQUEST = 4;
    public const TYPE_REJECTION = 5;

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'user_id' => true,
        'project_id' => true,
        'body' => true,
        'created' => true,
        'user' => true,
        'project' => true,
        'type' => true,
    ];

    /**
     * Returns TRUE if this note triggers an email being sent out
     *
     * @return bool
     */
    public function triggersEmail(): bool
    {
        return in_array($this->type, [
            self::TYPE_REJECTION,
            self::TYPE_REVISION_REQUEST,
            self::TYPE_MESSAGE_TO_APPLICANT,
        ]);
    }

    public static function getNoteTypePublicName(int $type, bool $isAdmin = false): string
    {
        return match ($type) {
            self::TYPE_NOTE => 'Internal note',
            self::TYPE_REJECTION => 'Your project could not be accepted',
            self::TYPE_MESSAGE_TO_APPLICANT => $isAdmin
                ? 'Message to applicant'
                : 'Message from the review committee',
            self::TYPE_MESSAGE_FROM_APPLICANT => $isAdmin
                ? 'Message from the applicant'
                : 'Message to the review committee',
            self::TYPE_REVISION_REQUEST => 'Revision request',
            default => 'Note (type unknown)',
        };
    }

    public static function getNoteTypeIcon(int $type): string
    {
        return match ($type) {
            self::TYPE_NOTE => Project::ICON_NOTE,
            self::TYPE_MESSAGE_TO_APPLICANT,
            self::TYPE_MESSAGE_FROM_APPLICANT => Project::ICON_MESSAGE,
            self::TYPE_REVISION_REQUEST => Project::ICON_REVISION_REQUESTED,
            self::TYPE_REJECTION => Project::ICON_REJECTED,
            default => Project::ICON_UNKNOWN,
        };
    }

    protected function _getTypeWithIcon(): string
    {
        return  self::getNoteTypeIcon($this->type) . ' ' . self::getNoteTypePublicName($this->type);
    }
}
