<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Transaction Entity
 *
 * @property int $id
 * @property int $type
 * @property int|null $amount
 * @property int|null $application_id
 * @property string $meta
 * @property \Cake\I18n\FrozenTime $created
 *
 * @property \App\Model\Entity\Application $application
 */
class Transaction extends Entity
{
    public const TYPE_DONATION = 1;

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
        'type' => true,
        'amount' => true,
        'application_id' => true,
        'meta' => true,
        'created' => true,
        'application' => true,
    ];
}
