<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * FundingCycle Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime|null $application_begin
 * @property \Cake\I18n\FrozenTime|null $application_end
 * @property \Cake\I18n\FrozenTime|null $vote_begin
 * @property \Cake\I18n\FrozenTime|null $vote_end
 * @property int $funding_available
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Application[] $applications
 * @property \App\Model\Entity\Vote[] $votes
 */
class FundingCycle extends Entity
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
        'application_begin' => true,
        'application_end' => true,
        'vote_begin' => true,
        'vote_end' => true,
        'funding_available' => true,
        'created' => true,
        'modified' => true,
        'applications' => true,
        'votes' => true
    ];
}
