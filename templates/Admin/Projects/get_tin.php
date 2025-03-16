<?php
/**
 * @var string|null $decrypted
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Project $project
 */
?>

<?php if ($project->tin): ?>
    <?php if ($decrypted): ?>
        <p class="alert alert-success">
            The tax ID number for <strong><?= $project->title ?></strong> is <strong><?= $decrypted ?></strong>.
        </p>
        <p class="alert alert-warning">
            Keep this information strictly confidential and be extremely careful about where it is saved
            (encryption / password protection highly recommended).
        </p>
    <?php else: ?>
        <p class="alert alert-info">
            To protect the applicant's sensitive data, the tax ID number associated with <strong><?= $project->title ?></strong> is encrypted. To retrieve it, find the decryption key in the organization's secure records and enter it below.
        </p>

        <?= $this->Form->create(null) ?>
        <?= $this->Form->control('secret', ['label' => 'Secret key']) ?>
        <?= $this->Form->submit() ?>
        <?= $this->Form->end() ?>
    <?php endif; ?>
<?php else: ?>
    <p class="alert alert-danger">
        <strong><?= $project->title ?></strong> does not have a tax ID number saved.
    </p>
<?php endif; ?>

<?php
sodium_memzero($decrypted);
?>
