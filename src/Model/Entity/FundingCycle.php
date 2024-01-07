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
 * @property \Cake\I18n\FrozenTime|null $application_begin_local
 * @property \Cake\I18n\FrozenTime|null $application_end_local
 * @property \Cake\I18n\FrozenTime|null $resubmit_deadline_local
 * @property \Cake\I18n\FrozenTime|null $vote_begin_local
 * @property \Cake\I18n\FrozenTime|null $vote_end_local
 * @property int $funding_available
 * @property string $funding_available_formatted
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $name
 *
 * @property \App\Model\Entity\Project[] $projects
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
        'projects' => true,
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
     * @return \Cake\Chronos\ChronosInterface|FrozenTime
     */
    protected function _getApplicationBeginLocal()
    {
        return $this->application_begin->setTimezone(\App\Application::LOCAL_TIMEZONE);
    }

    /**
     * @return \Cake\Chronos\ChronosInterface|FrozenTime
     */
    protected function _getApplicationEndLocal()
    {
        return $this->application_end->setTimezone(\App\Application::LOCAL_TIMEZONE);
    }

    /**
     * @return \Cake\Chronos\ChronosInterface|FrozenTime
     */
    protected function _getResubmitDeadlineLocal()
    {
        return $this->resubmit_deadline->setTimezone(\App\Application::LOCAL_TIMEZONE);
    }

    /**
     * @return \Cake\Chronos\ChronosInterface|FrozenTime
     */
    protected function _getVoteBeginLocal()
    {
        return $this->vote_begin->setTimezone(\App\Application::LOCAL_TIMEZONE);
    }

    /**
     * @return \Cake\Chronos\ChronosInterface|FrozenTime
     */
    protected function _getVoteEndLocal()
    {
        return $this->vote_end->setTimezone(\App\Application::LOCAL_TIMEZONE);
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

    /**
     * Returns an array with counts for submitted, accepted, and awarded projects,
     * or NULL if no applications have been received
     *
     * @return int[]|null
     */
    public function getProjectsSummary()
    {
        if (!$this->projects ?? false) {
            return null;
        }
        $retval = [
            'submitted' => 0,
            'accepted' => 0,
            'awarded' => 0,
        ];
        foreach ($this->projects as $project) {
            switch ($project->status_id) {
                // Submitted
                case Project::STATUS_UNDER_REVIEW:
                case Project::STATUS_ACCEPTED:
                case Project::STATUS_REJECTED:
                case Project::STATUS_REVISION_REQUESTED:
                case Project::STATUS_AWARDED:
                case Project::STATUS_NOT_AWARDED:
                    $retval['submitted']++;
                    break;
            }
            switch ($project->status_id) {
                // Accepted
                case Project::STATUS_ACCEPTED:
                case Project::STATUS_AWARDED:
                case Project::STATUS_NOT_AWARDED:
                    $retval['accepted']++;
                    break;
            }
            if ($project->status_id === Project::STATUS_AWARDED) {
                $retval['awarded']++;
            }
        }

        return $retval;
    }

    /**
     * Returns TRUE if this funding cycle is currently in its voting period
     *
     * @return bool
     */
    public function isCurrentlyVoting(): bool
    {
        $now = new \DateTime();
        return $this->vote_begin <= $now && $this->vote_end >= $now;
    }

    /**
     * Returns TRUE if voting has finished
     *
     * @return bool
     */
    public function votingHasPassed(): bool
    {
        $now = new \DateTime();
        return $this->vote_end <= $now;
    }

    public function convertToLocalTimes(): FundingCycle|static
    {
        $retval = $this;
        $retval->application_begin = $retval->application_begin_local;
        $retval->application_end = $retval->application_end_local;
        $retval->vote_begin = $retval->vote_begin_local;
        $retval->vote_end = $retval->vote_end_local;
        $retval->resubmit_deadline = $retval->resubmit_deadline_local;
        return $retval;
    }

    /**
     * @return string
     */
    protected function _getFundingAvailableFormatted(): string
    {
        return $this->funding_available
            ? '$' . number_format($this->funding_available)
            : 'Not yet determined';
    }
}
