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

use App\Model\Entity\Image;
use App\Model\Entity\Project;

$formId = 'project-form';
$this->Html->css('/viewerjs/viewer.min.css', ['block' => true]);
$this->Html->script('/viewerjs/viewer.min.js', ['block' => true]);
$defaultFormTemplate = include(CONFIG . 'bootstrap_form.php');
$data = $this->getRequest()->getData();
$saveMode = $data['save-mode'] ?? null;
$preloadImageData = array_map(function (Image $image) {
    return [
        'id' => $image->id,
        'filename' => $image->filename,
        'weight' => $image->weight,
        'caption' => $image->caption,
    ];
}, $project->images ?? []);

function getAgreementCheckedValue($key, $data, $project) {
    if (isset($data[$key])) {
        return $data[$key] ? 'checked="checked"' : '';
    }
    return $project->isNew() ? '' : 'checked="checked"';
}
?>

<?php if ($project->status_id == Project::STATUS_REVISION_REQUESTED): ?>
    <div class="alert alert-info">
        <p>
            <i class="fa-solid fa-circle-exclamation"></i>
            <strong>We'll need this project to be revised before we can accept it.</strong>
            For details,
            <?= $this->Html->link(
                'check this project\'s messages',
                [
                    'prefix' => 'My',
                    'controller' => 'Projects',
                    'action' => 'messages',
                    'id' => $project->id,
                ]
            ) ?>
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
    <?= $this->Form->create($project, ['enctype' => 'multipart/form-data', 'id' => $formId]) ?>
    <fieldset>
        <legend>
            Applicant Eligibility
        </legend>
        <p>
            To apply for funding, you must attest that the applicant
        </p>
        <?= $this->element('eligibility_applicant') ?>
        <div class="form-check required">
            <input type="hidden" name="eligibility-applicant-agree" value="0" />
            <input class="form-check-input" type="checkbox" value="1" id="eligibility-applicant-agree-checkbox"
                   required="required" name="eligibility-applicant-agree"
                   <?= getAgreementCheckedValue('eligibility-applicant-agree', $data, $project) ?>
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
            <input type="hidden" value="0" name="eligibility-project-agree" />
            <input class="form-check-input" type="checkbox" value="1" id="eligibility-project-agree-checkbox"
                   required="required" name="eligibility-project-agree"
                   <?= getAgreementCheckedValue('eligibility-project-agree', $data, $project) ?>
            >
            <label class="form-check-label" for="eligibility-project-agree-checkbox">
                This project qualifies for funding
            </label>
        </div>
    </fieldset>

    <fieldset>
        <legend>
            Application Terms
        </legend>
        <ol>
            <li>
                Reports about the status of this project must be submitted to the Vore Arts Fund at least annually and
                upon its completion. A form is provided on this website for submitting these reports, and loan
                recipients will be emailed reminders when reports are due.
            </li>
            <li>
                By applying for a loan, you grant the Vore Arts Fund the right to publicly release the provided
                information and media (excluding mailing address) to the public, which it may do for purposes such
                as transparency and promotion.
            </li>
            <li>
                The Vore Arts Fund does not claim project ownership nor a business relationship with the applicant.
                Applicants and loan recipients retain all ownership of and copyright to their intellectual property.
            </li>
            <li>
                Neither the acceptance of an application nor the funding of a project by the Vore Arts Fund constitutes support or endorsement of the views,
                opinions, or claims of applicants and loan recipients.
            </li>
        </ol>
        <div class="form-check required">
            <input type="hidden" value="0" name="loan-terms-agree" />
            <input class="form-check-input" type="checkbox" value="1" id="loan-terms-agree-checkbox" required="required"
                   name="loan-terms-agree"
                <?= getAgreementCheckedValue('loan-terms-agree', $data, $project) ?>
            >
            <label class="form-check-label" for="loan-terms-agree-checkbox">
                I agree to these terms
            </label>
        </div>
    </fieldset>

    <fieldset>
        <legend>
            Loan Terms
        </legend>

        <p>
            You will be asked to agree to these terms and provide your tax ID number before receiving a loan (but not right now).
        </p>

        <?= $this->element('loan_terms') ?>
    </fieldset>

    <fieldset>
        <legend>
            Funding Request
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

        <p class="alert alert-danger mt-2" style="display: none;" id="insufficient-budget-warning">
            The funding cycle that you are applying in only has <?= $fundingCycle->funding_available_formatted ?> budgeted, so it cannot fully
            fund your project.
        </p>
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

        <div class="form-group select required">
            <label for="description">
                Description of your project (please provide a detailed proposal, including goals, audience, and timeline)
            </label>
            <?= $this->Form->textarea(
                'description',
                [
                    'id' => 'description',
                    'required' => true,
                    'type' => 'textarea',
                ]
            ) ?>
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
            Images
        </legend>
        <p>
            Have images to help convey what your project is? Upload up to five of them here.
        </p>
        <div id="image-uploader-root"></div>
        <p>
            Sexually explicit or disturbingly violent imagery may not be included in an application and may result
            in disqualification.
        </p>
        <script>
            window.preloadImages = <?= json_encode($preloadImageData) ?>;
        </script>
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

    <div class="form-group save-mode">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="save-mode" id="save-draft" value="save"
                <?= $saveMode != 'submit' ? 'checked' : null ?>>
            <label class="form-check-label" for="save-draft">
                <span class="icon"><i class="fa-solid fa-floppy-disk"></i></span>
                Save as draft without submitting
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="save-mode" value="submit" id="save-submit"
                <?= $saveMode == 'submit' ? 'checked' : null ?>>
            <label class="form-check-label" for="save-submit">
                <span class="icon"><i class="fa-solid fa-paper-plane"></i></span>
                Submit to review committee
            </label>
        </div>
    </div>

    <?= $this->Form->button(
        'Submit',
        [
            'type' => 'submit',
            'class' => 'btn btn-primary',
        ]
    ) ?>
    <?= $this->Form->end() ?>
</div>

<?= $this->element('load_app_files', ['dir' => 'image-uploader']) ?>
<?= $this->element('expired_session_handler', compact('formId')) ?>

<script>
    const budget = <?= json_encode($fundingCycle->funding_available) ?>;
    const budgetFormatted = <?= json_encode($fundingCycle->funding_available_formatted) ?>;
    const radioButtons = document.querySelectorAll('input[name=accept_partial_payout]');
    const budgetWarning = document.getElementById('insufficient-budget-warning');
    const amountRequested = document.querySelector('input[name=amount_requested]');
    const refusePartialPayout = document.getElementById('accept-partial-payout-0');
    const toggleWarning = () => {
        budgetWarning.style.display = (amountRequested.value > budget && refusePartialPayout.checked)
            ? 'block'
            : 'none';
    };
    document.querySelectorAll('input[name=accept_partial_payout]').forEach(element => {
        element.addEventListener('change', toggleWarning);
    });
    amountRequested.addEventListener('change', () => {
        toggleWarning();
    });

    const toggleSaveModeOptionHighlight = () => {
        document.querySelectorAll('input[name="save-mode"]').forEach(radioBtn => {
            // Embolden checked option
            radioBtn.parentElement.querySelector('label').style.fontWeight = radioBtn.checked ? 'bold' : 'normal';

            if (!radioBtn.checked) {
                return;
            }

            // Update submit button text
            const submitBtn = document.getElementById(<?= json_encode($formId) ?>)
                .querySelector('button[type="submit"]');
            submitBtn.innerHTML = radioBtn.value[0].toUpperCase() + radioBtn.value.slice(1);
        });
    }
    document.querySelectorAll('input[name="save-mode"]').forEach(button => {
        button.addEventListener('change', toggleSaveModeOptionHighlight);
    });
    document.addEventListener('DOMContentLoaded', () => {
        toggleSaveModeOptionHighlight();

        // Workaround for this not working automatically without the delay when navigating back
        setTimeout(() => {
            toggleSaveModeOptionHighlight();
        }, 1000);
    });
</script>
