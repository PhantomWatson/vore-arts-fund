<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Transaction $transaction
 * @var \Cake\Collection\CollectionInterface|string[] $projects
 */

$types = \App\Model\Entity\Transaction::getTypes();
?>
<?php if ($this->getRequest()->getParam('action') == 'edit' && isset($_GET['delete'])): ?>
    <?= $this->Form->postLink(
        __('Delete'),
        ['action' => 'delete', $transaction->id],
        [
            'confirm' => __('Are you sure you want to delete # {0}?', $transaction->id),
            'class' => 'btn btn-danger',
        ]
    ) ?>
<?php endif; ?>
<?= $this->Form->create($transaction) ?>
<fieldset>
    <div class="form-group">
        <?= $this->Form->label('Date') ?>
        <?= $this->Form->date('date') ?>
    </div>

    <div class="form-group number">
        <?php
            echo $this->Form->label('Type');
            echo $this->Form->select(
                'type',
                $types,
                [
                    'empty' => true,
                    'id' => 'type',
                ],
            );
        ?>
    </div>
    <?php
        echo $this->Form->control('amount', ['type' => 'number', 'min' => 0, 'step' => 0.01]);
        echo $this->Form->control('project_id', ['options' => $projects, 'empty' => true]);
        echo $this->Form->control('meta');
    ?>
</fieldset>
<?= $this->Form->button(
     $this->getRequest()->getParam('action') == 'edit' ? 'Update' : 'Add',
    ['class' => 'btn btn-primary']
) ?>
<?= $this->Form->end() ?>
