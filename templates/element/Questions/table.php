<?php
/**
 * @var \App\Model\Entity\Question[] $questions
 */
?>

<table class="table">
    <thead>
        <tr>
            <th>Question</th>
            <th>Weight</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($questions as $question): ?>
            <tr>
                <td><?= $question->question ?></td>
                <td><?= $this->Number->format($question->weight) ?></td>
                <td class="actions">
                    <?= $this->Html->link(
                        'Edit',
                        ['action' => 'edit', $question->id],
                        ['class' => 'btn btn-secondary']
                    ) ?>
                    <?php if ($question->hasAnswers): ?>
                        <button class="btn btn-secondary disabled" title="Can't delete (question has answers)">
                            Delete
                        </button>
                    <?php else: ?>
                        <?= $this->Form->postLink(
                            'Delete',
                            ['action' => 'delete', $question->id],
                            [
                                'confirm' => __('Are you sure you want to delete # {0}?', $question->id),
                                'class' => 'btn btn-secondary',
                            ]
                        ) ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
