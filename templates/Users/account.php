<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var Application[] $applications
 * @var string $title
 */

use App\Model\Entity\Application;
?>

<?php if ($user->is_verified): ?>
    <p class="alert alert-success">
        Phone number verified
    </p>
<?php else: ?>
    <?= $this->Html->link(
        'Verify phone number',
        [
            'controller' => 'Users',
            'action' => 'verify',
        ],
        ['class' => 'btn btn-secondary']
    ) ?>
<?php endif; ?>

<?= $this->Html->link(
    'Change Account Info',
    [
        'controller' => 'Users',
        'action' => 'changeAccountInfo',
    ],
    ['class' => 'btn btn-secondary']
) ?>
