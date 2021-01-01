<?php
use Cake\ORM\TableRegistry;

$fundingCycle = TableRegistry::getTableLocator()->get('FundingCycles')->find()->where(['id' => $this->request->getParam('id')])->first();
?>

<div class="pb-2 mt-4 mb-2 border-bottom">
    <h1>Edit Funding Cycle</h1>
</div>
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
<?= $this->Form->button(__('Submit')) ?>
<?= $this->Form->end() ?>
