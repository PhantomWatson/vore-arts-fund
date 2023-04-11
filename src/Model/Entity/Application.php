<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\InternalErrorException;
use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;

/**
 * Application Entity
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property int $category_id
 * @property string $description
 * @property int $amount_requested
 * @property bool $accept_partial_payout
 * @property int $funding_cycle_id
 * @property string $check_name
 * @property int $status_id
 * @property string $status_name
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Answer[] $answers
 * @property \App\Model\Entity\Category $category
 * @property \App\Model\Entity\FundingCycle $funding_cycle
 * @property \App\Model\Entity\Image[] $images
 * @property \App\Model\Entity\Message[] $messages
 * @property \App\Model\Entity\Note[] $notes
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Vote[] $votes
 * @property \App\Model\Entity\Report[] $reports
 */
class Application extends Entity
{
    const STATUS_DRAFT = 0;
    const STATUS_UNDER_REVIEW = 1;
    const STATUS_ACCEPTED = 2;
    const STATUS_REJECTED = 3;
    const STATUS_REVISION_REQUESTED = 4;
    const STATUS_AWARDED = 6;
    const STATUS_NOT_AWARDED = 7;
    const STATUS_WITHDRAWN = 8;

    /**
     * Returns TRUE if this application can be viewed by the public
     *
     * @return bool
     */
    public function isViewable(): bool
    {
        $viewableStatuses = [
            Application::STATUS_ACCEPTED,
            Application::STATUS_AWARDED,
            Application::STATUS_NOT_AWARDED,
        ];

        return in_array($this->status_id, $viewableStatuses);
    }

    /**
     * Returns TRUE if this application can be updated by the applicant
     *
     * @return bool
     */
    public function isUpdatable(): bool
    {
        $updatableStatuses = [
            Application::STATUS_DRAFT,
            Application::STATUS_REVISION_REQUESTED,
        ];

        return in_array($this->status_id, $updatableStatuses);
    }

    /**
     * @return string[]
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT              => 'Draft',
            self::STATUS_UNDER_REVIEW       => 'Under Review',
            self::STATUS_ACCEPTED           => 'Accepted',
            self::STATUS_REJECTED           => 'Not Accepted',
            self::STATUS_REVISION_REQUESTED => 'Revision Requested',
            self::STATUS_AWARDED            => 'Awarded',
            self::STATUS_NOT_AWARDED        => 'Not Awarded',
            self::STATUS_WITHDRAWN          => 'Withdrawn',
        ];
    }

    /**
     * @return string[]
     */
    public static function getStatusActions(): array
    {
        return [
            self::STATUS_DRAFT              => 'Save this application as a draft',
            self::STATUS_UNDER_REVIEW       => 'Submit  this application for review',
            self::STATUS_ACCEPTED           => 'Accept this application',
            self::STATUS_REJECTED           => 'Reject this application',
            self::STATUS_REVISION_REQUESTED => 'Request revision',
            self::STATUS_AWARDED            => 'Award funding to this application',
            self::STATUS_NOT_AWARDED        => 'Decline to award funding to this application',
            self::STATUS_WITHDRAWN          => 'Withdraw this application',
        ];
    }

    /**
     * Takes a current status and returns an array of valid statuses that this application can be changed to
     *
     * @param int $currentStatusId
     * @return int[]
     */
    public static function getValidStatusOptions(int $currentStatusId)
    {
        switch ($currentStatusId) {
            case self::STATUS_DRAFT:
            case self::STATUS_REVISION_REQUESTED:
                return [
                    self::STATUS_UNDER_REVIEW,
                    self::STATUS_WITHDRAWN,
                ];
            case self::STATUS_UNDER_REVIEW:
                return [
                    self::STATUS_ACCEPTED,
                    self::STATUS_REJECTED,
                    self::STATUS_REVISION_REQUESTED,
                    self::STATUS_WITHDRAWN,
                ];
            case self::STATUS_ACCEPTED:
                return [
                    self::STATUS_AWARDED,
                    self::STATUS_NOT_AWARDED,
                    self::STATUS_WITHDRAWN,
                ];
            case self::STATUS_REJECTED:
            case self::STATUS_AWARDED:
            case self::STATUS_NOT_AWARDED:
            case self::STATUS_WITHDRAWN:
                return [];
        }

        throw new InternalErrorException("Unrecognized status: $currentStatusId");
    }

    /**
     * @param int $statusId
     * @return string
     * @throws \Cake\Http\Exception\InternalErrorException
     */
    public static function getStatus(int $statusId): string
    {
        $statuses = self::getStatuses();
        if (key_exists($statusId, $statuses)) {
            return $statuses[$statusId];
        }
        throw new InternalErrorException("Status #$statusId not recognized");
    }

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
        'title' => true,
        'category_id' => true,
        'description' => true,
        'amount_requested' => true,
        'accept_partial_payout' => true,
        'category' => true,
        'answers' => true,
        'images' => true,
        'check_name' => true,
        '*' => false,
    ];

    /**
     * @return string
     */
    protected function _getStatusName()
    {
        return self::getStatus($this->status_id);
    }

    /**
     * Returns the deadline to submit the current application (which may be in the "resubmit" window after the initial
     * application window)
     *
     * If application is draft, deadline is application_end
     * If application is revision-requested, deadline is resubmit_deadline
     *
     * @return \Cake\I18n\FrozenTime
     * @throws \Cake\Http\Exception\BadRequestException
     */
    public function getSubmitDeadline(): FrozenTime
    {
        switch ($this->status_id) {
            case Application::STATUS_DRAFT:
                return $this->funding_cycle->application_end;
            case Application::STATUS_REVISION_REQUESTED:
                return $this->funding_cycle->resubmit_deadline;
            default:
                throw new BadRequestException('That application cannot currently be updated.');
        }
    }
}
