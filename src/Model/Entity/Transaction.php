<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\Http\Exception\BadRequestException;
use Cake\ORM\Entity;

/**
 * Transaction Entity
 *
 * @property int $id
 * @property int $type
 * @property string $type_name
 * @property string $name Name of person who initiated the transaction, such as a donor
 * @property int|null $amount_gross Amount paid
 * @property int|null $amount_net Amount received
 * @property int|null $project_id Project for loans, loan repayments, or canceled checks
 * @property int|null $user_id ID of user who recorded the transaction
 * @property string $meta Check number, donor name, Stripe meta dump
 * @property float $dollar_amount_net
 * @property string $dollar_amount_net_formatted
 * @property float $dollar_amount_gross
 * @property string $dollar_amount_gross_formatted
 * @property string $processing_fee_formatted In dollars
 * @property \Cake\I18n\FrozenDate $date
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Project $project
 */
class Transaction extends Entity
{
    public const TYPE_DONATION = 1;
    public const TYPE_LOAN_REPAYMENT = 2;
    public const TYPE_LOAN = 3;
    public const TYPE_CANCELED_CHECK = 4;

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
        'amount_net' => true,
        'amount_gross' => true,
        'project_id' => true,
        'meta' => true,
        'created' => true,
        'project' => true,
        'date' => true,
        'name' => true,
    ];

    public static function getTypes()
    {
        return [
            self::TYPE_DONATION => 'Donation',
            self::TYPE_LOAN_REPAYMENT => 'Loan repayment',
            self::TYPE_LOAN => 'Loan',
            self::TYPE_CANCELED_CHECK => 'Canceled check',
        ];
    }

    /**
     * @param int $typeId
     * @return string
     * @throws BadRequestException
     */
    public static function getTypeName(int $typeId): string
    {
        $types = static::getTypes();
        if (isset($types[$typeId])) {
            return $types[$typeId];
        }
        throw new BadRequestException('Unrecognized transaction type: ' . $typeId);
    }

    /**
     * Returns amount in dollars
     *
     * @param int $amount Amount in cents
     * @return float
     */
    private function getDollarAmount($amount): float
    {
        return $amount ? $amount / 100 : 0;
    }

    /**
     * Returns a dollar-formatted string
     *
     * @return string
     */
    private function getDollarAmountFormatted($amount): string
    {
        return '$' . number_format($amount, 2);
    }

    protected function _getDollarAmountNet(): float
    {
        return $this->getDollarAmount($this->amount_net);
    }

    protected function _getDollarAmountNetFormatted(): string
    {
        return $this->getDollarAmountFormatted($this->dollar_amount_net);
    }

    protected function _getDollarAmountGross(): float
    {
        return $this->getDollarAmount($this->amount_gross);
    }

    protected function _getDollarAmountGrossFormatted(): string
    {
        return $this->getDollarAmountFormatted($this->dollar_amount_gross);
    }

    protected function _getTypeName(): string
    {
        return Transaction::getTypeName($this->type);
    }

    protected function _getProcessingFeeFormatted(): string
    {
        if ($this->amount_gross && $this->amount_net) {
            $fee = $this->amount_gross - $this->amount_net;
            return '$' . number_format($fee / 100, 2);
        }
        return '';
    }
}
