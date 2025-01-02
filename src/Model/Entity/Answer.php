<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\View\Helper\TextHelper;
use Cake\View\View;

/**
 * Answer Entity
 *
 * @property int $id
 * @property int $project_id
 * @property int $question_id
 * @property string $answer
 * @property string $formatted_answer
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Project $project
 * @property \App\Model\Entity\Question $question
 */
class Answer extends Entity
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
        'project_id' => true,
        'question_id' => true,
        'answer' => true,
        'created' => true,
        'modified' => true,
        'project' => true,
        'question' => true,
    ];

    /**
     * Returns the answer with stripped tags + added line/paragraph breaks + linked URLs and email addresses
     *
     * @return string
     */
    protected function _getFormattedAnswer(): string
    {
        $textHelper = new TextHelper(new View());
        $answer = $this->answer;
        $answer = strip_tags($answer);
        $answer = $textHelper->autoParagraph($answer);
        return $textHelper->autoLink($answer, ['escape' => false]);
    }
}
