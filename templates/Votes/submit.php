<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Application[] $application
 */

use Cake\ORM\TableRegistry;

$applications = TableRegistry::getTableLocator()->get('Applications')->find()->where(['status_id' => 5])->all()->toArray();
?>

<div class='pb-2 mt-4 mb-2 border-bottom'>
    <h1>Vote</h1>
</div>
<div>
    <?= $this->Flash->render() ?>
    <?= $this->Form->create() ?>
    <fieldset class="fieldset">
        <legend class="form"><?= __('Please select which applications you would like to vote for. (You may choose more than one)') ?></legend>
        <?php
        foreach ($applications as $application) {
            /** @var \App\Model\Entity\Image $image */
            $image = TableRegistry::getTableLocator()->get('Images')->find()->where(['application_id' => $application['id']])->first();
            if (isset($image) && !empty($image)) {
                echo $this->Html->image($image->path, ['alt' => $image->caption, 'height' => '200px', 'width' => '200px']);
            } else {
                echo 'No Image';
            }
            ?>
            <h3><?= $application['title'] ?></h3>
            <?= $this->Form->checkbox($application['id'], ['hiddenField' => false]) ?>
        <?php } ?>
    </fieldset>
    <?= $this->Form->button(__('Submit', ['type' => 'submit'])) ?>
    <?= $this->Form->end() ?>
</div>
