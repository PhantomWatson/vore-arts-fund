<?php
/**
 * @var \App\View\AppView $this
 */
?>

<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <?= $this->Html->link(
            'Account info',
            ['action' => 'changeAccountInfo'],
            ['class' => 'nav-link' . ($this->getRequest()->getParam('action') == 'changeAccountInfo' ? ' active' : '')]
        ) ?>
    </li>
    <li class="nav-item" role="presentation">
        <?= $this->Html->link(
            'Update password',
            ['action' => 'updatePassword'],
            ['class' => 'nav-link' . ($this->getRequest()->getParam('action') == 'updatePassword' ? ' active' : '')]
        ) ?>
    </li>
</ul>
