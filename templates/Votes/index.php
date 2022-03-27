<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Application[] $applications
 */
?>

<?= $this->title() ?>

<div>
    <?php foreach ($applications as $application): ?>
        <div>
            <h3><?= $application['title'] ?></h3>
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
        </div>
    <?php endforeach; ?>
    <?= $this->Html->link(
        'Vote',
        [
            'controller' => 'Votes',
            'action' => 'submit',
        ],
        ['class' => 'btn btn-primary']
    ) ?>
</div>
