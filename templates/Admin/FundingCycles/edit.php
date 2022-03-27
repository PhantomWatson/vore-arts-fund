<?php
/**
 * @var \App\Model\Entity\FundingCycle $fundingCycle
 */
?>

<?= $this->title() ?>

<?= $this->Flash->render() ?>
<?= $this->Form->create($fundingCycle) ?>
<fieldset>
    <?= $this->Form->control('application_begin') ?>
    <?= $this->Form->control('application_end') ?>
    <?= $this->Form->control('vote_begin') ?>
    <?= $this->Form->control('vote_end') ?>
    <?= $this->Form->control('funding_available') ?>
    <?= $this->Form->hidden('id') ?>
</fieldset>
<?= $this->Form->submit(__('Submit'), ['class' => 'btn btn-primary']) ?>
<?= $this->Form->end() ?>
