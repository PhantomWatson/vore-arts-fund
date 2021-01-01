<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Application[] $applications
 */
?>

<div class="pb-2 mt-4 mb-2 border-bottom">
    <h1>Applications</h1>
</div>

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
                ['class' => 'button']
            ) ?>
        </div>
    <?php endforeach; ?>
    <?= $this->Html->link(
        'Vote',
        [
            'controller' => 'Votes',
            'action' => 'submit',
        ],
        ['class' => 'button']
    ) ?>
</div>
