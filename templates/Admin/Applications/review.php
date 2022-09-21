<?php
/**
 * @var \App\Model\Entity\Application $application
 * @var \App\Model\Entity\Note $newNote
 * @var \App\Model\Entity\Note[]|\Cake\ORM\ResultSet $notes
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
            'prefix' => 'Admin',
            'controller' => 'Applications',
            'action' => 'index',
        ],
        ['class' => 'btn btn-secondary']
    ) ?>
</p>

<div class="row">
    <div class="col-md-6">
        <?= $this->element('../Applications/view') ?>
    </div>
    <div class="col-md-6 card" id="review-action-column">
        <section class="card-body">
            <h3>
                Status: <?= $application->status_name ?>
            </h3>
            <?php if ($statusOptions): ?>
                <?= $this->Form->create($application) ?>
                <label class="control-label" for="change-status">
                    Change status to
                </label>
                <select name="status_id" class="form-select" id="change-status">
                    <?php foreach ($statusOptions as $statusId => $statusName): ?>
                        <option value="<?= $statusId ?>">
                            <?= $statusName ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= $this->Form->submit('Update status', ['class' => 'btn btn-primary']) ?>
                <?= $this->Form->end() ?>
            <?php endif; ?>
        </section>

        <section class="card-body">
            <h3>
                Notes
            </h3>
            <?= $this->Form->create($newNote) ?>
            <?= $this->Form->control(
                'body',
                [
                    'type' => 'textarea',
                    'label' => false
                ]
            ) ?>
            <?= $this->Form->submit('Add note', ['class' => 'btn btn-primary']) ?>
            <?= $this->Form->end() ?>
            <?php if (!$notes->isEmpty()): ?>
                <div id="review-notes">
                    <?php foreach ($notes as $note): ?>
                        <section>
                            <h4>
                                <?= $note->user->name ?>
                            </h4>
                            <p>
                                <?= nl2br($note->body) ?>
                            </p>
                            <p class="date">
                                <?= $note->created->format('F j, Y') ?>
                            </p>
                        </section>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>
