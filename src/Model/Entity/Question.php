<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

/**
 * Question Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property bool $enabled
 * @property bool $hasAnswers
 * @property int $weight
 * @property string|null $question
 *
 * @property \App\Model\Entity\Answer[] $answers
 */
class Question extends Entity
{
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
        'question' => true,
        'enabled' => true,
        'weight' => true,
        'created' => true,
        'modified' => true,
        'answers' => true,
    ];

    /**
     * Returns TRUE if this question has associated answers
     *
     * @return bool
     */
    protected function _getHasAnswers(): bool
    {
        $answersTable = TableRegistry::getTableLocator()->get('Answers');
        return $answersTable->exists(['question_id' => $this->id]);
    }
}
