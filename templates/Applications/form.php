<?php
/**
 * @var \App\Model\Entity\Application $application
 * @var \App\Model\Entity\FundingCycle $fundingCycle
 * @var \App\Model\Entity\Question[] $questions
 * @var \App\View\AppView $this
 * @var string $deadline
 * @var string $fromNow
 * @var string[] $categories
 */

$this->Html->css('/filepond/filepond.css', ['block' => true]);
$this->Html->css('/filepond/filepond-plugin-image-preview.css', ['block' => true]);
?>

<?= $this->title() ?>

<p class="alert alert-info">
    The deadline to submit an application in the current funding cycle is
    <strong><?= $deadline ?></strong> (<?= $fromNow ?>).
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
            'category_id',
            ['empty' => true, 'required' => true]
        ) ?>

        <div class="form-group select required">
            <label for="description">
                Description of project
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
                What are you trying to accomplish?
            </p>
        </div>

        <?php foreach ($questions as $i => $question): ?>
            <div class="form-group select required">
                <label for="<?= "question-$i" ?>">
                    <?= $question->question ?>
                </label>
                <?= $this->Form->hidden("answers.$i.question_id", ['value' => $question->id]) ?>
                <?= $this->Form->textarea(
                    "answers.$i.answer",
                    [
                        'id' => "question-$i",
                        'required' => true,
                        'type' => 'textarea',
                    ]
                ) ?>
            </div>
        <?php endforeach; ?>
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
                'inputContainer' => '{{content}}',
            ]); ?>
            <div class="input-group mb-3">
                <span class="input-group-text">$</span>
                <?= $this->Form->control(
                    'amount_requested',
                    [
                        'required' => true,
                        'type' => 'number',
                        'step' => 1,
                    ]
                ) ?>
                <span class="input-group-text">.00</span>
            </div>
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
        <div class="form-group">
            <?= $this->Form->label(
                'filepond[]',
                'Have images to help convey what your project is? Upload them here.'
            ) ?>
            <?= $this->Form->control('filepond[]', [
                'accept' => 'image/*',
                'label' => false,
                'type' => 'file',
                'multiple' => true,
            ]) ?>
        </div>
    </fieldset>
    <?= $this->Form->submit(
        'Save for later',
        [
            'name' => 'save',
            'class' => 'btn btn-secondary',
        ]
    ) ?>
    <?= $this->Form->button(
        'Submit',
        [
            'type' => 'submit',
            'class' => 'btn btn-primary',
            'confirm' => 'Are you sure you\'re ready to submit this application for review?'
        ]
    ) ?>
    <?= $this->Form->end() ?>
</div>

<script src="/filepond/filepond-plugin-image-preview.js"></></script>
<script src="/filepond/filepond-plugin-file-validate-type.js"></></script>
<script src="/filepond/filepond-plugin-image-transform.js"></></script>
<script src="/filepond/filepond-plugin-image-resize.js"></></script>
<script src="/filepond/filepond.js"></></script>
<script>
    FilePond.registerPlugin(FilePondPluginImagePreview);
    FilePond.registerPlugin(FilePondPluginFileValidateType);
    FilePond.registerPlugin(FilePondPluginImageTransform);
    FilePond.registerPlugin(FilePondPluginImageResize);
    const pond = FilePond.create(
        document.querySelector('#customFile'),
        {
            // General
            allowMultiple: true,
            allowReorder: true,
            maxFiles: 5,
            server: {
                url: '/images/upload',
                process: {
                    // Massage data as it comes back from the upload endpoint
                    ondata: function (formData) {
                        let newFormData = new FormData();
                        for ([oldKey, image] of formData.entries()) {
                            if (image instanceof File) {
                                let newKey = image.name.includes('thumb_') ? 'thumb' : 'full';
                                newFormData.append(newKey, image);
                            }
                        }
                        return newFormData;
                    }
                },
            },

            // Validation
            acceptedFileTypes: ['image/*'],
            fileValidateTypeLabelExpectedTypes: 'Duh',
            labelFileTypeNotAllowed: 'Only images can be uploaded here',

            // Resizing
            imageResizeTargetWidth: 1000,
            imageResizeTargetHeight: 1000,
            imageResizeMode: 'contain',
            imageResizeUpscale: false,

            // Transforming
            imageTransformOutputMimeType: 'image/png',
            imageTransformVariants: {
                thumb_: (transforms) => {
                    transforms.resize = {
                        size: {
                            width: 128,
                            height: 128,
                        },
                    };
                    return transforms;
                },
            },
        },
    );
</script>
