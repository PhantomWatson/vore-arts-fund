<?php
/**
 * @var \App\View\AppView $this
 */

$this->assign('footer', $this->Html->link(__('Back'), 'javascript:history.back()'));
$this->assign('title', 'Error');
$this->extend('default');
echo $this->fetch('content');
