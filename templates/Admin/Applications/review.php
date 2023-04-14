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
            <section>
                <h3>
                    Status: <?= $application->status_name ?>
                </h3>
                <div id="root"></div>
            </section>

            <?php if (!$notes->isEmpty()): ?>
                <section>
                    <h3>
                        Notes
                    </h3>
                    <div id="review-notes">
                        <?php foreach ($notes as $note): ?>
                            <section>
                                <p>
                                    <?= nl2br($note->body) ?>
                                </p>
                                <p class="date">
                                    <?= $note->user->name ?> - <?= $note->created->format('F j, Y') ?>
                                </p>
                            </section>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    window.statusActions = <?= json_encode($statusActions) ?>;
    window.validStatusIds = <?= json_encode($validStatusIds) ?>;
</script>
<?= $this->element('load_app_files', ['dir' => 'review']) ?>
