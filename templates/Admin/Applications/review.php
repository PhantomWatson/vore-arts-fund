<?php
/**
 * @var \App\Model\Entity\Application $application
 * @var \App\Model\Entity\Note $newNote
 * @var \App\Model\Entity\Note[]|\Cake\ORM\ResultSet $notes
 * @var \App\Model\Entity\Question[] $questions
 * @var \App\View\AppView $this
 * @var int[] $statusActions
 * @var int[] $validStatusIds
 * @var string $title
 */

function getActionName($statusId, array $statusActions): string
{
    return array_search($statusId, $statusActions);
}
?>

<div class="row">
    <div class="col-md-6">
        <?= $this->element('../Applications/view') ?>
        <table class="table">
            <tbody>
                <tr>
                    <th>
                        Accept partial payout?
                    </th>
                    <td>
                        <?= $application->accept_partial_payout ? 'Yes' : 'No' ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        Make check out to
                    </th>
                    <td>
                        <?= $application->check_name ?: '<span class="no-answer">No answer</span>' ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-6 card" id="review-action-column">
        <div class="card-body">
            <div id="root"></div>
            <section>
                <h3>
                    Status: <?= $application->status_name ?>
                </h3>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Actions
                    </button>
                    <ul class="dropdown-menu" id="review-actions">
                        <li>
                            <a class="dropdown-item" href="#" id="review-action-note" data-action="note">
                                Add note
                            </a>
                        </li>
                        <?php foreach ($validStatusIds as $statusId): ?>
                            <li>
                                <a class="dropdown-item" href="#" data-status-id="<?= $statusId ?>" data-action="changeStatus">
                                    <?= getActionName($statusId, $statusActions) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?= $this->Form->create($application) ?>
                <?= $this->Form->control(
                    'note',
                    [
                        'id' => 'revision-requested-note',
                        'label' => 'What update are you requesting?',
                        'required' => true,
                        'type' => 'textarea',
                    ]
                ) ?>
                <?= $this->Form->submit('Update status', ['class' => 'btn btn-primary']) ?>
                <?= $this->Form->end() ?>
            </section>

            <section>
                <h3>
                    Notes
                </h3>
                <?= $this->Form->create($newNote) ?>
                <?= $this->Form->control(
                    'body',
                    [
                        'type' => 'textarea',
                        'label' => false,
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
</div>

<script>
    window.statusActions = <?= json_encode($statusActions) ?>;
    window.validStatusIds = <?= json_encode($validStatusIds) ?>;
</script>
<?= $this->element('load_app_files', ['dir' => 'review']) ?>
