<?php
/**
 * @var \App\View\AppView $this
 * @var string[] $categories
 */
?>
<div class="pb-2 mt-4 mb-2 border-bottom">
    <h1>Apply</h1>
</div>
<div class="apply">
    <?= $this->Flash->render() ?>
    <?= $this->Form->create(null, ['enctype' => 'multipart/form-data']) ?>
    <fieldset>
        <?= $this->Form->control('title', [
            'required' => true,
            'label' => 'The title of your project',
            'templateVars' => [
                'footnote' => 'This could be the actual title that your finished work will have or just a description of what you\'re trying to pay for, like "Canvases and paint" or "Fix theater sound system"'
            ],
            'type' => 'inputWithFootnote'
        ]) ?>

        <?= $this->Form->control(
            'category',
            ['empty' => true, 'required' => true]
        ) ?>

        <?= $this->Form->control('description', ['type' => 'textarea', 'required' => true]) ?>

        <?php $this->Form->setTemplates([
            'formGroup' => '{{input}}',
            'numberWithPrefixContainer' => '<div class="form-group {{type}}{{required}}"><label for="amount-requested">Amount Requested</label><div class="input-group mb-2 mr-sm-2"><div class="input-group-prepend"><div class="input-group-text">{{prefix}}</div></div>{{content}}</div></div>',
            'numberWithPrefixInput' => 'dsfsdafdsf<input class="form-control" type="number" name="{{name}}"{{attrs}} />',
        ]); ?>
        <?= $this->Form->control(
            'amount_requested',
            [
                'required' => true,
                'type' => 'numberWithPrefix',
                'templateVars' => ['prefix' => '$'],
            ]
        ) ?>
        <?php $this->Form->setTemplates([
            'formGroup' => '{{label}}{{input}}',
        ]); ?>

        <div class="form-group required accept-partial">
            <?= $this->Form->label('accept-partial-payout-0', 'Accept Partial Payout') ?>
            <?= $this->Form->radio('accept_partial_payout', ['Yes', 'No'], ['required' => true]) ?>
        </div>

        <?= $this->Form->file('image') ?>

        <?= $this->Form->control('imageCaption') ?>
    </fieldset>
    <?= $this->Form->submit(__('Save'), ['name' => 'save', 'class' => 'btn btn-primary']) ?>
    <?= $this->Form->submit('Submit', ['type' => 'submit', 'class' => 'btn btn-primary']) ?>
    <?= $this->Form->end() ?>
</div>
