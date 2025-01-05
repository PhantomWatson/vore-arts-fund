<?php
/**
 * @var \Cake\ORM\ResultSet|\App\Model\Entity\Note[] $notes
 * @var \App\Model\Entity\Project $project
 */

use App\Model\Entity\Project;
use Cake\Routing\Router;

$action = Router::url([
    'prefix' => 'My',
    'controller' => 'Projects',
    'action' => 'sendMessage',
    'id' => $project->id,
]);
$notesTable = \Cake\ORM\TableRegistry::getTableLocator()->get('Notes');
$newNote = $notesTable->newEmptyEntity();
?>

<p class="project-notes__send-message">
    <span>
        Have a question or additional information about this project to give the review committee?
    </span>
    <button class="btn btn-primary"
            type="button"
            data-bs-toggle="modal"
            data-bs-target="#message-to-review-committee-modal"
    >
        <?= Project::ICON_MESSAGE ?>
        &nbsp;
        Send a message
    </button>
</p>

<?php if ($notes->count()): ?>
    <?php foreach ($notes as $note): ?>
        <?= $this->element('Notes/view', compact('note')) ?>
    <?php endforeach; ?>
<?php else: ?>
    <p>
        You have received no messages about this project so far.
    </p>
<?php endif; ?>

<div class="modal fade" id="message-to-review-committee-modal" tabindex="-1"
     aria-labelledby="message-to-review-committee-modal-label" aria-hidden="true">
    <?= $this->Form->create($newNote, ['action' => $action]) ?>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="message-to-review-committee-modal-label">
                        Send message about this project
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group required">
                        <label for="message-body">
                            Enter your message below and it will be sent to the review committee.
                        </label>
                        <textarea name="body" class="form-control" rows="5" required="required" id="message-body"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        Send
                    </button>
                </div>
            </div>
        </div>
    <?= $this->Form->end() ?>
</div>
