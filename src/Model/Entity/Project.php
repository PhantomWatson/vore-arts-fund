<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\InternalErrorException;
use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;

/**
 * Project Entity
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property int $category_id
 * @property string $description
 * @property int $amount_requested In dollars
 * @property string $amount_requested_formatted e.g. $1,234
 * @property bool $accept_partial_payout
 * @property int $funding_cycle_id
 * @property string $check_name
 * @property int $status_id
 * @property string $status_name
 * @property float $voting_score
 * @property int $amount_awarded In dollars
 * @property int $amount_awarded_formatted e.g. $1,234
 * @property string $status_summary
 * @property \Cake\I18n\FrozenTime $loan_agreement_date
 * @property \Cake\I18n\FrozenTime $loan_due_date
 * @property int $loan_agreement_version
 * @property string $tin
 * @property string $address
 * @property string $zipcode
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
 * @property \App\Model\Entity\Transaction[] $transactions
 */
class Project extends Entity
{
    const STATUS_DRAFT = 0;
    const STATUS_UNDER_REVIEW = 1;
    const STATUS_ACCEPTED = 2;
    const STATUS_REJECTED = 3;
    const STATUS_REVISION_REQUESTED = 4;
    const STATUS_AWARDED_NOT_YET_DISBURSED = 5;
    const STATUS_AWARDED_AND_DISBURSED = 6;
    const STATUS_NOT_AWARDED = 7;
    const STATUS_WITHDRAWN = 8;

    const ICON_ACCEPTED = '<i class="fa-solid fa-thumbs-up"></i>';
    const ICON_REJECTED = '<i class="fa-solid fa-heart-crack"></i>';
    const ICON_REVISION_REQUESTED = '<i class="fa-solid fa-rotate-left"></i>';
    const ICON_MESSAGE = '<i class="fa-solid fa-message"></i>';
    const ICON_NOTE = '<i class="fa-solid fa-file-lines"></i>';
    const ICON_FUND = '<i class="fa-solid fa-sack-dollar"></i>';
    const ICON_UNKNOWN = '<i class="fa-solid fa-question"></i>';
    const ICON_SAVE = '<i class="fa-solid fa-floppy-disk"></i>';
    const ICON_SUBMIT = '<i class="fa-solid fa-share-from-square"></i>';
    const ICON_WITHDRAW = '<i class="fa-solid fa-ban"></i>';
    const ICON_REPORT = '<i class="fa-solid fa-file-lines"></i>';

    /** @var int Maximum amount that can be requested (in dollars) */
    const MAXIMUM_ALLOWED_REQUEST = 1000000; // One million dollars

    const VIEWABLE_STATUSES = [
        Project::STATUS_ACCEPTED,
        Project::STATUS_AWARDED_NOT_YET_DISBURSED,
        Project::STATUS_AWARDED_AND_DISBURSED,
        Project::STATUS_NOT_AWARDED,
    ];

    // When, starting from the signing of the loan agreement, the loan is automatically considered canceled
    const DUE_DATE = '5 years';

    /**
     * Returns TRUE if this project can be viewed by the public
     *
     * @return bool
     */
    public function isViewable(): bool
    {
        return in_array($this->status_id, self::VIEWABLE_STATUSES);
    }

    /**
     * Returns TRUE if this project can be updated by the applicant
     *
     * @return bool
     */
    public function isUpdatable(): bool
    {
        $updatableStatuses = [
            Project::STATUS_DRAFT,
            Project::STATUS_REVISION_REQUESTED,
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
            self::STATUS_AWARDED_NOT_YET_DISBURSED => 'Awarded (Disbursement Pending)',
            self::STATUS_AWARDED_AND_DISBURSED     => 'Awarded',
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
            self::STATUS_DRAFT => [
                'icon' => self::ICON_SAVE,
                'label' => 'Save this application as a draft',
            ],
            self::STATUS_UNDER_REVIEW => [
                'icon' => self::ICON_SUBMIT,
                'label' => 'Submit this application for review',
            ],
            self::STATUS_ACCEPTED => [
                'icon' => self::ICON_ACCEPTED,
                'label' => 'Accept this application',
            ],
            self::STATUS_REJECTED => [
                'icon' => self::ICON_REJECTED,
                'label' => 'Reject this application',
            ],
            self::STATUS_REVISION_REQUESTED => [
                'icon' => self::ICON_REVISION_REQUESTED,
                'label' => 'Request revision',
            ],
            self::STATUS_AWARDED_NOT_YET_DISBURSED => [
                'icon' => self::ICON_FUND,
                'label' => 'Award funding to this project',
            ],
            self::STATUS_AWARDED_AND_DISBURSED => [
                'icon' => self::ICON_FUND,
                'label' => 'Mark project as having had funding disbursed',
            ],
            self::STATUS_NOT_AWARDED => [
                'icon' => self::ICON_REJECTED,
                'label' => 'Decline to award funding to this project',
            ],
            self::STATUS_WITHDRAWN => [
                'icon' => self::ICON_WITHDRAW,
                'label' => 'Withdraw this application',
            ],
        ];
    }

    /**
     * @param int $statusId
     * @return array [icon, label]
     */
    public static function getStatusAction(int $statusId): array
    {
        $actions = self::getStatusActions();
        if (key_exists($statusId, $actions)) {
            return $actions[$statusId];
        }
        throw new InternalErrorException("Unrecognized status: $statusId");
    }

    /**
     * Takes a current status and returns an array of valid statuses that this project can be changed to
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
                    self::STATUS_AWARDED_NOT_YET_DISBURSED,
                    self::STATUS_NOT_AWARDED,
                    self::STATUS_WITHDRAWN,
                ];
            case self::STATUS_AWARDED_NOT_YET_DISBURSED:
                return [
                    self::STATUS_AWARDED_AND_DISBURSED,
                    self::STATUS_WITHDRAWN,
                ];
            case self::STATUS_REJECTED:
            case self::STATUS_AWARDED_AND_DISBURSED:
            case self::STATUS_NOT_AWARDED:
            case self::STATUS_WITHDRAWN:
                return [];
        }

        throw new InternalErrorException("Unrecognized status: $currentStatusId");
    }

    /**
     * Returns the name of a status
     *
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

    public static function getStatusesNeedingMessages(): array
    {
        return [
            self::STATUS_REVISION_REQUESTED,
            self::STATUS_REJECTED,
        ];
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
            case Project::STATUS_DRAFT:
                return $this->funding_cycle->application_end;
            case Project::STATUS_REVISION_REQUESTED:
                return $this->funding_cycle->resubmit_deadline;
            default:
                throw new BadRequestException('That application cannot currently be updated.');
        }
    }

    /**
     * Returns the sum of all vote weights
     *
     * @return float|null
     */
    protected function _getVotingScore()
    {
        if (!isset($this->votes) || empty($this->votes)) {
            return null;
        }

        $total = 0;
        foreach ($this->votes as $vote) {
            $total += $vote->weight;
        }
        return $total;
    }

    /**
     * @return string
     */
    protected function _getAmountRequestedFormatted()
    {
        return ($this->accept_partial_payout ? 'Up to ' : '') . '$' . number_format($this->amount_requested);
    }

    protected function _getStatusSummary()
    {
        $retval = '';
        $startsWithVowel = in_array(substr($this->category->name, 0, 1), ['a', 'e', 'i', 'o', 'u']);
        switch ($this->category->name) {
            case 'Other':
                $retval .= 'A miscellaneous';
                break;
            default:
                $retval .= ($startsWithVowel ? 'An ' : 'A ') . lcfirst($this->category->name);
        }
        //$isPast = ($this->funding_cycle ?? false) ? $this->funding_cycle->votingHasPassed() : true;
        $isPast = $this->funding_cycle->votingHasPassed();
        $retval .= " project by {$this->user->name}, who ";
        $retval .= $isPast ? 'requested ' : 'is requesting ';
        $retval .= strtolower($this->amount_requested_formatted);
        return $retval;
    }

    protected function _getAmountAwardedFormatted(): string
    {
        return '$' . number_format($this->amount_requested);
    }

    /**
     * Returns the latest loan terms version number
     *
     * Assumes that src/LoanTerms contains files named loan_terms_1.php, loan_terms_2.php, etc.
     *
     * @return int
     */
    public static function getLatestTermsVersion()
    {
        $templateDir = APP . 'LoanTerms';
        $files = array_diff(scandir($templateDir), ['.', '..']);
        $files = array_filter($files, function ($file) {
            return str_contains($file, 'loan_terms_');
        });
        $versionNumbers = array_map(
            function ($file) {
                return (int)str_replace(['loan_terms_', '.php'], '', $file);
            },
            $files
        );
        return $versionNumbers ? max($versionNumbers) : 0;
    }

    /**
     * Returns TRUE if this project has been given funding, or if we intend to fund it
     *
     * @return bool
     */
    public function isAwarded(): bool
    {
        return in_array(
            $this->status_id,
            [
                self::STATUS_AWARDED_NOT_YET_DISBURSED,
                self::STATUS_AWARDED_AND_DISBURSED,
            ]
        );
    }

    public function isDisbursed(): bool
    {
        return $this->status_id == self::STATUS_AWARDED_AND_DISBURSED;
    }
}
