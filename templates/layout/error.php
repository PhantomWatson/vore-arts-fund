<?php
/**
 * @var \App\View\AppView $this
 */

$this->assign('title', 'Error');
$this->extend('default');
echo $this->fetch('content');
