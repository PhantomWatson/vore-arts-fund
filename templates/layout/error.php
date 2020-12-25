<?php
/**
 * @var \App\View\AppView $this
 */

$this->extend('layout/default');
$this->assign('footer', $this->Html->link(__('Back'), 'javascript:history.back()'));
$this->assign('title', 'Error');
