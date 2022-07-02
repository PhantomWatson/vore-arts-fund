<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Application[] $applications
 */

use Cake\ORM\TableRegistry;
?>

<div>
    <?= $this->Form->create() ?>
    <fieldset class="fieldset">
        <legend class="form">
            <?= __('Please select which applications you would like to vote for. (You may choose more than one)') ?>
        </legend>
        <?php
        foreach ($applications as $application):
            /** @var \App\Model\Entity\Image $image */
            $image = TableRegistry::getTableLocator()
                ->get('Images')
                ->find()
                ->where(['application_id' => $application['id']])
                ->first();
            if (isset($image) && !empty($image)) {
                echo $this->Html->image(
                    $image->path,
                    [
                        'alt' => $image->caption,
                        'height' => '200px',
                        'width' => '200px',
                    ]
                );
            } else {
                echo 'No Image';
            }
            ?>
            <h3><?= $application['title'] ?></h3>
            <?= $this->Form->checkbox($application['id'], ['hiddenField' => false]) ?>
        <?php endforeach; ?>
    </fieldset>
    <?= $this->Form->submit(__('Submit'), ['class' => 'btn btn-primary']) ?>
    <?= $this->Form->end() ?>
</div>
