<?php
/**
 * @var \App\View\AppView $this
 */
$links = [
    'Account info' => 'changeAccountInfo',
    'Update password' => 'updatePassword',
    'Verify phone number' => 'verify',
];
?>

<ul class="nav nav-tabs" id="myTab" role="tablist">
    <?php foreach ($links as $label => $action): ?>
        <li class="nav-item" role="presentation">
            <?= $this->Html->link(
                $label,
                ['action' => $action],
                ['class' => 'nav-link' . ($this->getRequest()->getParam('action') == $action ? ' active' : '')]
            ) ?>
        </li>
    <?php endforeach; ?>
</ul>
