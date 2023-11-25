<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Transaction $transaction
 */

function isValidJson($data) {
    if (is_string($data) && !empty($data)) {
        @json_decode($data);
        return (json_last_error() === JSON_ERROR_NONE);
    }
    return false;
}

function printJson($data) {
    if (is_string($data)) {
        $data = json_decode($data);
    }
    $data = (array)$data;

    echo '<ul>';
    foreach ($data as $var => $val) {
        echo '<li>';
        echo '<strong>' . $var . '</strong>';
        if (is_string($val)) {
            if (isValidJson($val)) {
                printJson($val);
            } else {
                echo ': ' . $val;
            }
        } else {
            printJson($val);
        }
        echo '</li>';
    }
    echo '</ul>';
}
?>

<?= $this->Html->link(
    __('Edit'),
    ['action' => 'edit', $transaction->id],
    ['class' => 'btn btn-secondary']
) ?>
<?php if (isset($_GET['delete'])): ?>
    <?= $this->Form->postLink(
        __('Delete'),
        ['action' => 'delete', $transaction->id],
        ['confirm' => __('Are you sure you want to delete this transaction?'),
            'class' => 'btn btn-danger'
        ]) ?>
<?php endif; ?>

<table class="table">
    <tr>
        <th><?= __('Type') ?></th>
        <td><?= $transaction->type_name ?></td>
    </tr>
    <tr>
        <th><?= __('Amount') ?></th>
        <td><?= $transaction->dollar_amount_formatted ?></td>
    </tr>
    <tr>
        <th><?= __('Created') ?></th>
        <td><?= h($transaction->created) ?></td>
    </tr>
    <tr>
        <th>Project</th>
        <td>
            <?=
                $transaction->has('project')
                    ? $this->Html->link(
                        $transaction->project->title,
                        ['controller' => 'Projects', 'action' => 'view', $transaction->project->id]
                    )
                    : ''
            ?>
        </td>
    </tr>
    <tr>
        <th>
            Meta
        </th>
        <td>
            <?php if (isValidJson($transaction->meta)): ?>
                <?php printJson($transaction->meta) ?>
            <?php else: ?>
                <?= $this->Text->autoParagraph(h($transaction->meta)); ?>
            <?php endif; ?>
        </td>
    </tr>
</table>
