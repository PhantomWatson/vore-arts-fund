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
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $question->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $question->id], ['confirm' => __('Are you sure you want to delete # {0}?', $question->id)]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
