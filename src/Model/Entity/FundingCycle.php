<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;
use Cake\Routing\Router;

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
 * @property bool $is_finalized
 *
 * @property \App\Model\Entity\Project[] $projects
 * @property \App\Model\Entity\Vote[] $votes
 */
class FundingCycle extends Entity
{
    const TIME_START_FIELDS = [
        'application_begin',
        'vote_begin',
    ];
    const TIME_END_FIELDS = [
        'application_end',
        'resubmit_deadline',
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
        'is_finalized' => true,
    ];

    /**
     * Virtual field for the name of the funding cycle, which is the month when disbursement takes place,
     * which is assumed to take place a week after voting ends
     *
     * @return string
     */
    public function _getName(): string
    {
        return $this->vote_end->addWeek()->format('F Y');
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
                case Project::STATUS_AWARDED_NOT_YET_DISBURSED:
                case Project::STATUS_AWARDED_AND_DISBURSED:
                case Project::STATUS_NOT_AWARDED:
                    $retval['submitted']++;
                    break;
            }
            switch ($project->status_id) {
                // Accepted
                case Project::STATUS_ACCEPTED:
                case Project::STATUS_AWARDED_NOT_YET_DISBURSED:
                case Project::STATUS_AWARDED_AND_DISBURSED:
                case Project::STATUS_NOT_AWARDED:
                    $retval['accepted']++;
                    break;
            }
            switch ($project->status_id) {
                case Project::STATUS_AWARDED_NOT_YET_DISBURSED:
                case Project::STATUS_AWARDED_AND_DISBURSED:
                    $retval['awarded']++;
                    break;
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
     * Returns TRUE if this funding cycle is currently in its application period
     *
     * @return bool
     */
    public function isCurrentlyApplying(): bool
    {
        $now = new \DateTime();
        return $this->application_begin <= $now && $this->application_end >= $now;
    }

    /**
     * Returns TRUE if the application period has not yet started
     *
     * @return bool
     */
    public function awaitingApplicationPeriod(): bool
    {
        $now = new \DateTime();
        return $this->application_begin > $now;
    }

    /**
     * Returns TRUE if the voting period has not yet started
     *
     * @return bool
     */
    public function awaitingVotingPeriod(): bool
    {
        $now = new \DateTime();
        return $this->vote_begin > $now;
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

    public function getStatusDescription($addLinks = false): string
    {
        if ($this->is_finalized) {
            return 'Concluded';
        }

        if ($this->awaitingApplicationPeriod()) {
            return 'Not taking applications until ' . $this->application_begin_local->i18nFormat('MMM d, YYYY');
        }

        if ($this->isCurrentlyApplying()) {
            $retval = 'Currently accepting applications';
            $url = Router::url(['controller' => 'Projects', 'action' => 'apply', 'prefix' => false]);
            return $addLinks ? sprintf('<a href="%s">%s</a>', $url, $retval) : $retval;
        }

        if ($this->awaitingVotingPeriod()) {
            return 'Awaiting the start of voting on ' . $this->vote_begin_local->i18nFormat('MMM d, YYYY');
        }

        if ($this->isCurrentlyVoting()) {
            $retval = 'Voting currently underway';
            $url = Router::url(['controller' => 'Votes', 'action' => 'index', 'prefix' => false]);
            return $addLinks ? sprintf('<a href="%s">%s</a>', $url, $retval) : $retval;
        }

        if ($this->votingHasPassed()) {
            return 'Voting concluded and results being processed';
        }

        return 'Unknown';
    }
}
