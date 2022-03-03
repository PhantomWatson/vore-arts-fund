<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var \App\Model\Entity\Application[] $applications
 * @var string $title
 */
?>

<div class="pb-2 mt-4 mb-2 border-bottom">
    <h1>
        <?= $title ?>
    </h1>
</div>
<?= $this->Html->link(
    'Change Account Info',
    [
        'controller' => 'Users',
        'action' => 'changeAccountInfo',
    ],
    ['class' => 'btn btn-secondary']
) ?>

<h2>Applications</h2>
<?php foreach ($applications as $application): ?>
    <div>
        <h3><?= $application['title'] ?></h3>
        <?php if ($application['status_id'] === 8): ?>
            <p>Status: Withdrawn</p>
        <?php endif; ?>
        <?= $this->Html->link(
            'View',
            [
                'controller' => 'Applications',
                'action' => 'view',
                'id' => $application['id'],
                'slug' => '/view-application//',
            ],
            ['class' => 'btn btn-secondary']
        ) ?>
        <?php if ($application['status_id'] !== 8) {
            echo $this->Html->link(
                'Withdraw',
                [
                    'controller' => 'Applications',
                    'action' => 'withdraw',
                    'id' => $application['id'],
                ],
                ['class' => 'btn btn-secondary']
            );
        } ?>
        <?php if ($application['status_id'] === 8) {
            echo $this->Html->link(
                'Resubmit',
                [
                    'controller' => 'Applications',
                    'action' => 'resubmit',
                    'id' => $application['id'],
                ],
                ['class' => 'btn btn-secondary']
            );
        } ?>

        <?php if (in_array($application['status_id'], [1, 4, 8])) {
            echo $this->Html->link(
                'Delete',
                [
                    'controller' => 'Applications',
                    'action' => 'delete',
                    'id' => $application['id'],
                ],
                ['class' => 'btn btn-secondary']
            );
        } ?>
    </div>
<?php endforeach; ?>
