<?php
/**
 * @var \App\View\AppView $this
 * @var string[] $categories
 */
?>
<div class="pb-2 mt-4 mb-2 border-bottom">
    <h1>Apply</h1>
</div>
<div class = "apply">
    <?= $this->Flash->render() ?>
    <?= $this->Form->create(null, ['enctype' => 'multipart/form-data']) ?>
    <fieldset>
        <legend><?= __('Please enter the following information') ?></legend>
        <?= $this->Form->control('title', ['required' => true]) ?>
        <?= $this->Form->control('description', ['type' => 'textarea', 'required' => true]) ?>
        <?= $this->Form->label('Category') ?>
        <?= $this->Form->select(
            'category_id',
            $categories,
            ['empty' => true, 'required' => true]
        ) ?>
        <?= $this->Form->control('amount_requested', ['type' => 'number', 'required' => true]) ?>
        <?= $this->Form->label('Accept Partial Payout') ?>
        <?= $this->Form->radio('accept_partial_payout', ['Yes', 'No'], ['required' => true]) ?>
        <?= $this->Form->file('image') ?>
        <?= $this->Form->control('imageCaption') ?>
    </fieldset>
    <?= $this->Form->submit(__('Save'), ['name' => 'save', 'class' => 'btn btn-primary']) ?>
    <?= $this->Form->submit('Submit', ['type' => 'submit', 'class' => 'btn btn-primary']) ?>
    <?= $this->Form->end() ?>
</div>
