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
 * @property int $status_id
 * @property string $status_name
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Category $category
 * @property \App\Model\Entity\FundingCycle $funding_cycle
 * @property \App\Model\Entity\Image[] $images
 * @property \App\Model\Entity\Message[] $messages
 * @property \App\Model\Entity\Note[] $notes
 * @property \App\Model\Entity\Vote[] $votes
 */
class Application extends Entity
{
    const STATUS_DRAFT = 0;
    const STATUS_UNDER_REVIEW = 1;
    const STATUS_ACCEPTED = 2;
    const STATUS_REJECTED = 3;
    const STATUS_REVISION_REQUESTED = 4;
    const STATUS_VOTING = 5;
    const STATUS_AWARDED = 6;
    const STATUS_NOT_AWARDED = 7;
    const STATUS_WITHDRAWN = 8;

    /**
     * Returns TRUE if this event can be viewed by the public
     *
     * @return bool
     */
    public function isViewable(): bool
    {
        $viewableStatuses = [
            Application::STATUS_VOTING,
            Application::STATUS_AWARDED,
            Application::STATUS_NOT_AWARDED,
        ];

        return in_array($this->status_id, $viewableStatuses);
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
            self::STATUS_REJECTED           => 'Rejected',
            self::STATUS_REVISION_REQUESTED => 'Revision Requested',
            self::STATUS_VOTING             => 'Voting',
            self::STATUS_AWARDED            => 'Awarded',
            self::STATUS_NOT_AWARDED        => 'Not Awarded',
            self::STATUS_WITHDRAWN          => 'Withdrawn',
        ];
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
        'user_id' => true,
        'title' => true,
        'category_id' => true,
        'description' => true,
        'amount_requested' => true,
        'accept_partial_payout' => true,
        'funding_cycle_id' => false,
        'status_id' => false,
        'created' => true,
        'modified' => true,
        'user' => false,
        'category' => true,
        'funding_cycle' => false,
        'images' => true,
        'messages' => false,
        'notes' => false,
        'votes' => false,
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
