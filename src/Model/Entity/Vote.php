<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Vote Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int $application_id
 * @property int $funding_cycle_id
 * @property int $weight
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Application $application
 * @property \App\Model\Entity\FundingCycle $funding_cycle
 */
class Vote extends Entity
{
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
        'application_id' => true,
        'funding_cycle_id' => true,
        'weight' => true,
        'created' => true,
        'modified' => true,
        'user' => true,
        'application' => true,
        'funding_cycle' => true
    ];
}
