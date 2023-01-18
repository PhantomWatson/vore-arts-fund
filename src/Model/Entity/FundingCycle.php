<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;

/**
 * FundingCycle Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime|null $application_begin
 * @property \Cake\I18n\FrozenTime|null $application_end
 * @property \Cake\I18n\FrozenTime|null $resubmit_deadline
 * @property \Cake\I18n\FrozenTime|null $vote_begin
 * @property \Cake\I18n\FrozenTime|null $vote_end
 * @property int $funding_available
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $name
 *
 * @property \App\Model\Entity\Application[] $applications
 * @property \App\Model\Entity\Vote[] $votes
 */
class FundingCycle extends Entity
{
    const TIME_FIELDS = [
        'application_begin',
        'application_end',
        'resubmit_deadline',
        'vote_begin',
        'vote_end'
    ];

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
        'resubmit_deadline' => true,
        'vote_begin' => true,
        'vote_end' => true,
        'funding_available' => true,
        'created' => true,
        'modified' => true,
        'applications' => true,
        'votes' => true,
    ];

    /**
     * Virtual field for the name of the funding cycle, based on year and season when disbursement takes place
     *
     * Assumes that there will never be more than one in the same season
     *
     * @return string
     */
    public function _getName(): string
    {
        $disbursementDate = $this->vote_end->addDay(1);
        $year = $disbursementDate->format('Y');
        $disbursementMonth = $disbursementDate->format('n');
        return $this->getSeasonName($disbursementMonth) . ' ' . $year;
    }

    /**
     * Returns the name of the provided month's season
     *
     * 12-2: Winter
     * 3-5: Spring
     * 6-8: Summer
     * 9-11: Fall
     *
     * @param int $monthNumber
     * @return string
     */
    public function getSeasonName($monthNumber): string
    {
        if ($monthNumber == 12 || $monthNumber < 3) {
            return 'Winter';
        }
        if ($monthNumber < 6) {
            return 'Spring';
        }
        if ($monthNumber < 9) {
            return 'Summer';
        }
        return 'Fall';
    }

    protected function _getApplicationBegin(?FrozenTime $time): ?FrozenTime
    {
        return $time ? $time->setTimezone(\App\Application::LOCAL_TIMEZONE) : null;
    }

    protected function _getApplicationEnd(?FrozenTime $time): ?FrozenTime
    {
        return $time ? $time->setTimezone(\App\Application::LOCAL_TIMEZONE) : null;
    }

    protected function _getVoteBegin(?FrozenTime $time): ?FrozenTime
    {
        return $time ? $time->setTimezone(\App\Application::LOCAL_TIMEZONE) : null;
    }

    protected function _getVoteEnd(?FrozenTime $time): ?FrozenTime
    {
        return $time ? $time->setTimezone(\App\Application::LOCAL_TIMEZONE) : null;
    }

    protected function _getResubmitDeadline(?FrozenTime $time): ?FrozenTime
    {
        return $time ? $time->setTimezone(\App\Application::LOCAL_TIMEZONE) : null;
    }

    /**
     * Returns an array with counts for submitted, accepted, and awarded applications,
     * or NULL if no applications have been received
     *
     * @return int[]|null
     */
    public function getApplicationSummary()
    {
        if (!$this->applications ?? false) {
            return null;
        }
        $retval = [
            'submitted' => 0,
            'accepted' => 0,
            'awarded' => 0,
        ];
        foreach ($this->applications as $application) {
            switch ($application->status_id) {
                // Submitted
                case Application::STATUS_UNDER_REVIEW:
                case Application::STATUS_ACCEPTED:
                case Application::STATUS_REJECTED:
                case Application::STATUS_REVISION_REQUESTED:
                case Application::STATUS_AWARDED:
                case Application::STATUS_NOT_AWARDED:
                    $retval['submitted']++;
                    break;
            }
            switch ($application->status_id) {
                // Accepted
                case Application::STATUS_ACCEPTED:
                case Application::STATUS_AWARDED:
                case Application::STATUS_NOT_AWARDED:
                    $retval['accepted']++;
                    break;
            }
            if ($application->status_id === Application::STATUS_AWARDED) {
                $retval['awarded']++;
            }
        }

        return $retval;
    }
}
