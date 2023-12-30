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

$tabs = ['Overview' => 'overview', 'Description' => 'description', 'Notes & Messages' => 'notes'];
?>

<div class="col-md-6 mb-3 card" id="review-action-column">
    <div class="card-body">
        <section class="mb-0">
            <div id="root"></div>
        </section>
    </div>
</div>

<ul class="nav nav-tabs" id="review-tabs" role="tablist">
    <?php foreach ($tabs as $label => $tabId): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $tabId == 'overview' ? 'active' : '' ?>" id="<?= $tabId ?>-tab" data-bs-toggle="tab"
                    data-bs-target="#<?= $tabId ?>-section" type="button" role="tab" aria-controls="<?= $tabId ?>-section"
                    aria-selected="<?= $tabId == 'overview' ? 'true' : 'false' ?>">
                <?= $label ?>
                <?php if ($tabId == 'notes' && !$notes->isEmpty()): ?>
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
                Notes & Messages
            </h3>
            <div id="review-notes">
                <?php if ($notes->isEmpty()): ?>
                    None found for this project
                <?php else: ?>
                    <?php foreach ($notes as $note): ?>
                        <article class="note">
                            <header class="note-header row">
                                <p class="note-type col-6">
                                    <?= ucfirst($note->type) ?>
                                </p>
                                <p class="note-date col-6">
                                    <?= $note->user->name ?> - <?= $note->created->setTimezone(\App\Application::LOCAL_TIMEZONE)->format('F j, Y') ?>
                                </p>
                            </header>
                            <p>
                                <?= nl2br($note->body) ?>
                            </p>
                        </article>
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
