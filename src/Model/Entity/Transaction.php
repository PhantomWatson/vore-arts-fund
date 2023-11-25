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
 * @property int|null $amount Amount in cents
 * @property int|null $project_id Project for loans, loan repayments, or canceled checks
 * @property string $meta Check number, donor name, Stripe meta dump
 * @property float $dollar_amount
 * @property string $dollar_amount_formatted
 * @property \Cake\I18n\FrozenDate $date
 * @property \Cake\I18n\FrozenTime $created
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
        'amount' => true,
        'project_id' => true,
        'meta' => true,
        'created' => true,
        'project' => true,
        'date' => true,
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
     * Returns amount, but in dollars
     *
     * @return float
     */
    protected function _getDollarAmount(): float
    {
        return $this->amount ? $this->amount / 100 : 0;
    }

    protected function _getDollarAmountFormatted(): string
    {
        return '$' . number_format($this->dollar_amount, 2);
    }

    protected function _getTypeName(): string
    {
        return Transaction::getTypeName($this->type);
    }
}
