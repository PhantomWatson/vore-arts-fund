<?php
/**
 * @var \App\Model\Entity\Application $application
 * @var \App\View\AppView $this
 */
?>
<?= $this->title() ?>

<p>
    <strong>Category:</strong> <?= $application->category->name ?>
</p>

<p>
    <strong>Funding cycle:</strong> <?= $application->funding_cycle->name ?>
</p>

<p>
    <?= nl2br($application->description) ?>
</p>

<?php if ($application->images): ?>
    <?php foreach ($application->images as $image): ?>
        <div class="card" style="width: 18rem;">
            <img src="/img/applications/<?= $image->filename ?>" class="card-img-top image-thumbnail" alt="<?= $image->caption ?>" />
            <div class="card-body">
                <p class="card-text">
                    <?= $image->caption ?>
                </p>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= $this->Html->link(
    'Back',
    [
        'controller' => 'Votes',
        'action' => 'index',
    ],
    ['class' => 'btn btn-secondary']
) ?>
