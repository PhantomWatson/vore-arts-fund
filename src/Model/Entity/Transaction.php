<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Transaction Entity
 *
 * @property int $id
 * @property int $type
 * @property int|null $amount Amount in cents
 * @property int|null $project_id Project for loans, loan repayments, or canceled checks
 * @property string $meta Check number, donor name, Stripe meta dump
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
}
