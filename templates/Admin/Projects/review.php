<?php
/**
 * @var \App\Model\Entity\Project $project
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

<div class="col-md-6 mb-3 card" id="review-action-column">
    <div class="card-body">
        <section class="mb-0">
            <div id="root"></div>
        </section>
    </div>
</div>

<ul class="nav nav-tabs" id="review-tabs" role="tablist">
    <?php foreach (['overview', 'description', 'notes'] as $i => $tab): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= !$i ? 'active' : '' ?>" id="<?= $tab ?>-tab" data-bs-toggle="tab"
                    data-bs-target="#<?= $tab ?>-section" type="button" role="tab" aria-controls="<?= $tab ?>-section"
                    aria-selected="<?= !$i ? 'true' : 'false' ?>">
                <?= ucwords($tab) ?>
                <?php if ($tab == 'notes' && !$notes->isEmpty()): ?>
                    (<?= number_format(count($notes)) ?>)
                <?php endif; ?>
            </button>
        </li>
    <?php endforeach; ?>
</ul>

<div class="tab-content mt-3">
    <div class="tab-pane show active" id="overview-section" role="tabpanel" aria-labelledby="overview-tab">
        <table class="table project-overview-table">
            <tbody>
                <?= $this->element('Projects/overview_admin') ?>
            </tbody>
        </table>
    </div>
    <div class="tab-pane" id="description-section" role="tabpanel" aria-labelledby="description-tab">
        <?= $this->element('Projects/description') ?>
    </div>
    <div class="tab-pane" id="notes-section" role="tabpanel" aria-labelledby="notes-tab">
        <section>
            <h3>
                Notes
            </h3>
            <div id="review-notes">
                <?php if ($notes->isEmpty()): ?>
                    No notes have been added for this project yet.
                <?php else: ?>
                    <?php foreach ($notes as $note): ?>
                        <section>
                            <?php if ($note->type != \App\Model\Entity\Note::TYPE_NOTE): ?>
                                <p class="note-type">
                                    <?= ucfirst($note->type) ?>
                                </p>
                            <?php endif; ?>
                            <p>
                                <?= nl2br($note->body) ?>
                            </p>
                            <p class="date">
                                <?= $note->user->name ?> - <?= $note->created->setTimezone(\App\Application::LOCAL_TIMEZONE)->format('F j, Y') ?>
                            </p>
                        </section>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<script>
    window.statusActions = <?= json_encode($statusActions) ?>;
    window.validStatusIds = <?= json_encode($validStatusIds) ?>;
</script>
<?= $this->element('load_app_files', ['dir' => 'review']) ?>
