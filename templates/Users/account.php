<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var Application[] $applications
 * @var string $title
 */

use App\Model\Entity\Application;
?>

<?= $this->Html->link(
    'Change Account Info',
    [
        'controller' => 'Users',
        'action' => 'changeAccountInfo',
    ],
    ['class' => 'btn btn-secondary']
) ?>
