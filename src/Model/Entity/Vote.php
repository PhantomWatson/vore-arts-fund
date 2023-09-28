<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\Http\Exception\InternalErrorException;
use Cake\ORM\Entity;

/**
 * Vote Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int $project_id
 * @property int $funding_cycle_id
 * @property int $weight
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Project $project
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
        'project_id' => true,
        'funding_cycle_id' => true,
        'weight' => true,
        'created' => true,
        'modified' => true,
        'user' => true,
        'project' => true,
        'funding_cycle' => true,
    ];

    /**
     * When a project is placed in the nth position on a voter’s list of x selected projects, its score will be
     * increased by 1-(n-1)/x.
     *
     * Example: If a voter selects five projects, those projects’ scores will increase by 1.0, 0.8, 0.6, 0.4, and 0.2
     * points, respective to how that voter ranked them.
     *
     * @param int $rank Position (1-indexed) that an application was placed at by a voter
     * @param int $selectedProjectCount Total count of all applications voted on by a voter
     * @return float|int
     * @throws InternalErrorException
     */
    public static function calculateWeight(int $rank, int $selectedProjectCount)
    {
        if ($rank < 1 || $rank > $selectedProjectCount) {
            throw new InternalErrorException("Invalid rank: $rank of $selectedProjectCount");
        }

        return (1 - (($rank - 1) / $selectedProjectCount));
    }
}
