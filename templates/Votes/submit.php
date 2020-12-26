<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var \App\View\AppView $this
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
