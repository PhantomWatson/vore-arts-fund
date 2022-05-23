<?php
/**
 * @var \App\Model\Entity\Application $application
 * @var \App\Model\Entity\Category $category
 * @var \App\Model\Entity\Image $image
 * @var \App\View\AppView $this
 * @var string $title
 * @var string[] $statusOptions
 */
?>

<?= $this->title() ?>

<div>
    <h4>Status</h4>
    <?= $this->Form->create() ?>
    <fieldset>
        <?= $this->Form->control(
            'status_id',
            [
                'type' => 'select',
                'options' => $statusOptions,
                'label' => false,
                'empty' => 'Category',
                'default' => $application->status_id,
            ]
        ) ?>
    </fieldset>
    <?= $this->Form->submit(__('Update Status'), ['class' => 'btn btn-secondary']) ?>
    <?= $this->Form->end() ?>
</div>
<div>
    <h4>Description:</h4>
    <p><?= $application['description'] ?></p>
</div>
<div>
    <h4>Category:</h4>
    <p><?= $category[$application['category_id'] - 1]['name'] ?></p>
</div>
<div>
    <h4>Images:</h4>
            <?php
            if (isset($image) && !empty($image)) {
                echo $this->Html->image(
                    $image->path,
                    [
                        'alt' => $image->caption,
                        'height' => '200px',
                        'width' => '200px',
                    ]
                );
            }
            ?>
            <p>Caption: <?= $image->caption ?></p>
</div>
<form>
    <h4>Comment</h4>
    <?= $this->Form->create() ?>
    <fieldset>
        <?= $this->Form->textarea('Comment') ?>
    </fieldset>
    <?= $this->Form->submit(__('Comment'), ['class' => 'btn btn-secondary']) ?>
    <?= $this->Form->end() ?>
</form>
<?= $this->Html->link(
    'Back',
    [
        'controller' => 'Admin',
        'action' => 'applications',
    ],
    ['class' => 'btn btn-secondary']
) ?>
