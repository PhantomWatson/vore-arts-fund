<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Nudge Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int $project_id
 * @property int $type
 * @property \Cake\I18n\FrozenTime $created
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Project $project
 */
class Nudge extends Entity
{
    const TYPE_REPORT_REMINDER = 1;
    const TYPE_REPORT_DUE = 2;

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
        'project_id' => true,
        'type' => true,
        'created' => true,
        'user' => true,
        'project' => true,
    ];
}
