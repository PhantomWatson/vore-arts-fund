<?php
/**
 * @var \App\Model\Entity\Application $application
 * @var \App\Model\Entity\FundingCycle $fundingCycle
 * @var \App\View\AppView $this
 * @var string[] $categories
 */
?>
<div class="pb-2 mt-4 mb-2 border-bottom">
    <h1>
        Apply for Funding
    </h1>
</div>
<p class="alert alert-info">
    The deadline to submit an application in the current funding cycle is
    <strong><?= $fundingCycle->application_end->format('F j, Y') ?></strong>.
    For more information about future opportunities for funding, refer to the
    <?= $this->Html->link(
        'Funding Cycles',
        [
            'controller' => 'FundingCycles',
            'action' => 'index',
        ]
    ) ?> page.
</p>

<div class="apply">
    <?= $this->Flash->render() ?>
    <?= $this->Form->create($application, ['enctype' => 'multipart/form-data']) ?>
    <fieldset>
        <legend>
            Project
        </legend>
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

        <label for="description">
            Description
        </label>
        <?= $this->Form->textarea(
            'description',
            [
                'id' => 'description',
                'required' => true,
                'type' => 'textarea',
            ]
        ) ?>
        <p class="footnote">
            Tell us what you're trying to accomplish, what expenses you need help covering, and what your plan is
            for generating money with your project.
        </p>
    </fieldset>

    <fieldset>
        <legend>
            Payout
        </legend>
        <div class="form-group required">
            <label for="amount-requested">
                Amount Requested
            </label>
            <?php $this->Form->setTemplates([
                'formGroup' => '{{input}}',
            ]); ?>
            <?= $this->Form->control(
                'amount_requested',
                [
                    'required' => true,
                    'label' => 'foo',
                    'type' => 'number',
                    'templateVars' => ['prefix' => '$'],
                ]
            ) ?>
            <?php $this->Form->setTemplates([
                'formGroup' => '{{label}}{{input}}',
            ]); ?>
        </div>
        <div class="form-group required accept-partial">
            <?= $this->Form->label('accept-partial-payout-0', 'Would you accept a partial payout?') ?>
            <?= $this->Form->radio('accept_partial_payout', ['Yes', 'No'], ['required' => true]) ?>
            <p class="footnote">
                We may not have the budget to pay out this full amount. Would you still like to be considered for a
                smaller amount?
            </p>
        </div>
    </fieldset>

    <fieldset>
        <legend>
            Image
        </legend>
        <p>
            Have an image to help convey what your project is? Include it here.
        </p>
        <?= $this->Form->label('customFile', 'Image') ?>
        <?= $this->Form->file('image', ['label' => false]) ?>
        <?= $this->Form->control('imageCaption') ?>
    </fieldset>
    <?= $this->Form->submit(
        'Save for later',
        ['name' => 'save', 'class' => 'btn btn-secondary']
    ) ?>
    <?= $this->Form->submit(
        'Submit',
        ['type' => 'submit', 'class' => 'btn btn-primary']
    ) ?>
    <?= $this->Form->end() ?>
</div>
