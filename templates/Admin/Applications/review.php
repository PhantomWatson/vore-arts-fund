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

$application = TableRegistry::getTableLocator()->get('Applications')->get($this->request->getParam('id'));
$category = TableRegistry::getTableLocator()->get('Categories')->find()->all()->toArray();
$image = TableRegistry::getTableLocator()->get('Images')->find()->where(['application_id' => $application['id']])->first();
$statuses = TableRegistry::getTableLocator()->get('Statuses')->find()->all();
$statusOptions = [];
foreach ($statuses as $status) {
    $statusOptions[$status->id] = $status->name;
}
?>
<div class='pb-2 mt-4 mb-2 border-bottom'>
    <h1><?= $application['title'] ?></h1>
</div>
<div>
    <h4>Status</h4>
    <?= $this->Form->create() ?>
    <fieldset>
        <?= $this->Form->control('status_id', ['type' => 'select', 'options' => $statusOptions, 'label' => false, 'empty' => 'Category', 'default' => $application->status_id]) ?>
    </fieldset>
    <?= $this->Form->button(__('Update Status'), ['class' => 'button']); ?>
    <?= $this->Form->end() ?>
</div>
<div>
    <h4>Description:</h4>
    <p><?= $application['description'] ?></p>
</div>
<div>
    <h4>Category:</h4>
    <p><?= $category[($application['category_id'] - 1)]['name']; ?></p>
</div>
<div>
    <h4>Images:<h4>
            <?php
            if (isset($image) && !empty($image)) {
                echo $this->Html->image($image->path, ['alt' => $image->caption, 'height' => '200px', 'width' => '200px']);
            }
            ?>
            <p>Caption: <?= $image->caption; ?></p>
</div>
<form>
    <h4>Comment</h4>
    <?= $this->Form->create() ?>
    <fieldset>
        <?= $this->Form->textarea('Comment') ?>
    </fieldset>
    <?= $this->Form->button(__('Comment')); ?>
    <?= $this->Form->end() ?>
</form>
<?= $this->Html->link(
    'Back',
    '/admin/applications',
    ['class' => 'button']
) ?>

