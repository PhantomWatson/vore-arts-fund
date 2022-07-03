<?php
/**
 * @var \App\Model\Entity\Application $application
 * @var \App\Model\Entity\Question[] $questions
 * @var \App\View\AppView $this
 * @var string $title
 * @var string[] $statusOptions
 */
?>

<p>
    <?= $this->Html->link(
        'Back',
        [
            'controller' => 'Admin',
            'action' => 'applications',
        ],
        ['class' => 'btn btn-secondary']
    ) ?>
</p>

<div>
    <div class="row">
        <fieldset>
            <p>
                Status:
                <strong>
                    <?= $application->status_name ?>
                </strong>
            </p>
            <form class="form-inline" method="patch">
                <label class="control-label">
                    Change status to
                </label>
                <select name="status" class="form-select" id="change-status">
                    <?php foreach ($statusOptions as $statusId => $statusName): ?>
                        <option value="<?= $statusId ?>">
                            <?= $statusName ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary">
                    Update status
                </button>
            </form>
        </fieldset>
    </div>

    <form class="row row-cols-lg-auto g-3 align-items-center">
        <div class="col-12">

            <div class="form-group form-inline">

            </div>
        </div>
        <div class="col-12">

        </div>
    </form>
</div>

<?= $this->element('../Applications/view') ?>

<form>
    <h4>Comment</h4>
    <?= $this->Form->create() ?>
    <fieldset>
        <?= $this->Form->textarea('Comment') ?>
    </fieldset>
    <?= $this->Form->submit(__('Comment'), ['class' => 'btn btn-secondary']) ?>
    <?= $this->Form->end() ?>
</form>
