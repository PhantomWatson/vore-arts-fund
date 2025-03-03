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
<?= $this->Form->create($transaction, ['id' => 'transaction-form']) ?>
<fieldset id="form__transaction">
    <div class="form-group">
        <?= $this->Form->label('Date') ?>
        <?= $this->Form->dateTime('date') ?>
    </div>

    <div class="form-group number required">
        <?= $this->Form->label('Type') ?>
        <?= $this->Form->select(
            'type',
            $types,
            [
                'empty' => true,
                'id' => 'type',
            ],
        ) ?>
    </div>

    <label for="name">
        Name
    </label>
    <div class="row">
        <div class="col-sm-6">
            <?= $this->Form->control('name', ['label' => false]) ?>
        </div>
        <div class="col-sm-6">
            <p>The person or business that money was paid to or received from</p>
        </div>
    </div>

    <div class="required">
        <label for="amount-gross">
            Amount (gross)
        </label>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $this->Form->control('amount_gross', ['label' => false, 'type' => 'number', 'min' => 0, 'step' => 0.01]) ?>
        </div>
        <div class="col-sm-6">
            <p>The amount paid or received</p>
        </div>
    </div>

    <div class="required">
        <label for="amount-net">
            Amount (net)
        </label>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?= $this->Form->control('amount_net', ['label' => false, 'type' => 'number', 'min' => 0, 'step' => 0.01]) ?>
        </div>
        <div class="col-sm-6">
            <p>The gross amount, minus any processing fees</p>
        </div>
    </div>

    <label for="project-id">
        Project
    </label>
    <div class="row">
        <div class="col-sm-6">
            <?= $this->Form->control('project_id', ['label' => false, 'options' => $projects, 'empty' => true]) ?>
        </div>
        <div class="col-sm-6">
            <p>(Optional) The project that this transaction is associated with</p>
        </div>
    </div>

    <label for="meta">
        Metadata
    </label>
    <div class="row">
        <div class="col-sm-6">
            <?= $this->Form->control('meta', ['label' => false]) ?>
        </div>
        <div class="col-sm-6">
            <p>
                This is for any other information about this transaction that's important to remember.
                For any checks written, the check number should be recorded here.
            </p>
        </div>
    </div>
</fieldset>
<button type="submit" class="btn btn-primary">
    <?= $this->getRequest()->getParam('action') == 'edit' ? 'Update' : 'Add' ?>
</button>
<?= $this->Form->end() ?>

<script>
    preventMultipleSubmit('#transaction-form');
</script>
