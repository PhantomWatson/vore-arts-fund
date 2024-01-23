<?php
/**
 * @var Project $project
 * @var \App\Model\Entity\FundingCycle $fundingCycle
 * @var \App\Model\Entity\Question[] $questions
 * @var \App\Model\Entity\User $user
 * @var \App\View\AppView $this
 * @var string $deadline
 * @var string $fromNow
 * @var string[] $categories
 */

use App\Model\Entity\Project;

$this->Html->css('/filepond/filepond.css', ['block' => true]);
$this->Html->css('/filepond/filepond-plugin-image-preview.css', ['block' => true]);
$this->Html->css('/viewerjs/viewer.min.css', ['block' => true]);
$defaultFormTemplate = include(CONFIG . 'bootstrap_form.php');
?>

<?php if ($project->status_id == Project::STATUS_REVISION_REQUESTED): ?>
    <div class="alert alert-info">
        <p>
            <strong>We'll need this project to be revised before we can accept it.</strong>
            Check your inbox for a message with the subject
            "<?= \App\Event\MailListener::getRevisionRequestedSubject() ?>" for more information.
        </p>
        <p>
            The deadline for finalizing your application is
            <?= $fundingCycle->resubmit_deadline_local->format('F j, Y') ?>, after which you won't be able to
            update it. If it hasn't been revised and accepted by that date, then it won't be eligible for funding in
            this funding cycle.
        </p>
    </div>
<?php else: ?>
    <p class="alert alert-info">
        The deadline to submit an application in the current funding cycle is
        <strong><?= $deadline ?></strong> (<?= $fromNow ?>).
        For more information about future opportunities for funding, refer to the
        <?= $this->Html->link(
            'Funding Cycles',
            [
                'prefix' => false,
                'controller' => 'FundingCycles',
                'action' => 'index',
            ]
        ) ?> page.
    </p>
<?php endif; ?>

<div class="apply">
    <?= $this->Form->create($project, ['enctype' => 'multipart/form-data']) ?>
    <fieldset>
        <legend>
            Applicant Eligibility
        </legend>
        <p>
            To apply for funding, you must attest that the applicant
        </p>
        <?= $this->element('eligibility_applicant') ?>
        <div class="form-check required">
            <input class="form-check-input" type="checkbox" value="" id="eligibility-applicant-agree-checkbox" required="required"
                <?= $project->isNew() ? '' : 'checked="checked"' ?>
            >
            <label class="form-check-label" for="eligibility-applicant-agree-checkbox">
                I am eligible to apply
            </label>
        </div>
    </fieldset>

    <fieldset>
        <legend>
            Project Eligibility
        </legend>
        <p>
            To apply for funding, you must attest that the project for which funding is requested
        </p>
        <?= $this->element('eligibility_project') ?>
        <div class="form-check required">
            <input class="form-check-input" type="checkbox" value="" id="eligibility-project-agree-checkbox" required="required"
                <?= $project->isNew() ? '' : 'checked="checked"' ?>
            >
            <label class="form-check-label" for="eligibility-project-agree-checkbox">
                This project qualifies for funding
            </label>
        </div>
    </fieldset>

    <fieldset>
        <legend>
            Loan Terms
        </legend>

        <?= $this->element('loan_terms') ?>
        <div class="form-check required">
            <input class="form-check-input" type="checkbox" value="" id="loan-terms-agree-checkbox" required="required"
                <?= $project->isNew() ? '' : 'checked="checked"' ?>
            >
            <label class="form-check-label" for="loan-terms-agree-checkbox">
                I agree to these terms
            </label>
        </div>
    </fieldset>

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

        <div class="input-with-footnote">
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
            <div class="input-with-footnote">
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <?= $this->Form->control(
                        'amount_requested',
                        [
                            'required' => true,
                            'type' => 'number',
                            'step' => 1,
                            'max' => Project::MAXIMUM_ALLOWED_REQUEST,
                        ]
                    ) ?>
                    <span class="input-group-text">.00</span>
                </div>
                <p class="footnote">
                    Please round to the nearest dollar
                </p>
            </div>
            <?php $this->Form->setTemplates([
                'formGroup' => $defaultFormTemplate['formGroup'],
                'inputContainer' => $defaultFormTemplate['inputContainer'],
            ]); ?>
        </div>

        <?= $this->Form->control('check_name', ['label' => 'Who should the check should be made out to?']) ?>

        <div class="input-with-footnote">
            <div class="form-group required accept-partial">
                <?= $this->Form->label('accept-partial-payout-0', 'Would you accept a partial payout?') ?>
                <?= $this->Form->radio('accept_partial_payout', [1 => 'Yes', 0 => 'No'], ['required' => true]) ?>
                <p class="footnote">
                    We may not have the budget to pay out this full amount. Would you still like to be considered for a
                    smaller amount?
                </p>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend>
            Images
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
            <p>
                Sexually explicit or disturbingly violent imagery may not be included in an application and may result
                in disqualification.
            </p>
        </div>
        <?php if ($project->images): ?>
            <table class="image-gallery table" id="form-images">
                <thead>
                    <tr>
                        <th>
                            Delete?
                        </th>
                        <th>
                            Image
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($project->images as $image): ?>
                        <tr>
                            <td class="delete">
                                <label class="visually-hidden" for="delete-image-<?= $image->id ?>">
                                    Delete this image
                                </label>
                                <input type="checkbox" name="delete-image[]" value="<?= $image->id ?>"
                                       id="delete-image-<?= $image->id ?>" />
                            </td>
                            <td>
                                <?= $this->Image->thumb($image) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </fieldset>

    <fieldset>
        <legend>
            Mailing address
        </legend>
        <p>
            <?php if ($project->address && $project->zipcode): ?>
                Please confirm that your mailing address is still correct, and update it if needed.
                Note that this must be a Muncie address.
            <?php else: ?>
                Before you apply for funding, we need to know your mailing address so we'll know where to send your
                check. Note that this must be a Muncie address.
            <?php endif; ?>
        </p>

        <div class="form-group required">
            <label for="amount-requested">
                Street address
            </label>
            <?php $this->Form->setTemplates([
                'formGroup' => '{{input}}',
                'inputContainer' => '{{content}}',
            ]); ?>
            <div class="input-group mb-3">
                <?= $this->Form->control(
                    'address',
                    [
                        'placeholder' => '123 N. Example Blvd.',
                        'required' => true,
                    ]
                ) ?>
                <span class="input-group-text" id="address-postfix">, Muncie, IN</span>
            </div>
            <?php $this->Form->setTemplates([
                'formGroup' => $defaultFormTemplate['formGroup'],
                'inputContainer' => $defaultFormTemplate['inputContainer'],
            ]); ?>
        </div>
        <?= $this->Form->control('zipcode', ['label' => 'ZIP code']) ?>
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
                            width: 300,
                            height: 300,
                        },
                    };
                    return transforms;
                },
            },
        },
    );
</script>

<?= $this->Image->initViewer() ?>
