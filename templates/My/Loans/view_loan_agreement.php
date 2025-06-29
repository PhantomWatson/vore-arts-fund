<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Project $project
 */

$dueDate = $project->loan_due_date->setTimezone(new DateTimeZone(\App\Application::LOCAL_TIMEZONE))->format('F j, Y');
$amount = $project->amount_awarded_formatted;
?>

<?= $this->element('Projects/loan_agreement_preamble', compact('project')) ?>

<section class="loan-agreement">
    <h2>
        Terms
    </h2>
    <?= $this->element('loan_terms', compact('amount', 'dueDate')) ?>
</section>

<section class="loan-agreement">
    <h2>
        Signature
    </h2>
    <p>
        Signed, <?= $project->loan_agreement_signature ?>
        <br />
        <?= $project->loan_agreement_date->setTimezone(\App\Application::LOCAL_TIMEZONE)->format('F j, Y') ?>
    </p>
</section>

