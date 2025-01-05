<?php
/**
 * @var \App\Model\Entity\Project $project
 * @var string $noteType
 */

use App\Model\Entity\Note;
use Cake\Routing\Router;

$typeForId = $noteType == Note::TYPE_NOTE ? 'note' : 'message';
$action = Router::url([
    'prefix' => 'Admin',
    'controller' => 'Projects',
    'action' => 'newNote',
    'id' => $project->id,
]);
?>

<div class="modal fade" id="review-modal-<?= $typeForId ?>" tabindex="-1"
     aria-labelledby="review-modal-<?= $typeForId ?>-label" aria-hidden="true">
    <form method="post" action="<?= $action ?>">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="review-modal-<?= $typeForId ?>-label">
                        <?= $typeForId == 'note' ? 'Add note' : 'Send message' ?>
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>
                        <?php if ($typeForId == 'note'): ?>
                            Enter your note below. This will only be shared internally and not with the applicant.
                        <?php else: ?>
                            Enter your message below. This will be emailed to the applicant and will also be accessible through their "My Projects" page.
                        <?php endif; ?>
                    </p>

                    <div class="form-group required">
                        <label for="<?= $typeForId ?>-body">
                            <?= ucfirst($typeForId) ?>
                        </label>
                        <textarea name="body" class="form-control" rows="5" required="required" id="<?= $typeForId ?>-body"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="type" value="<?= $noteType ?>" />
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <?= $typeForId == 'note' ? 'Save' : 'Send' ?>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
