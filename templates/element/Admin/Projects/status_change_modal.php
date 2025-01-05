<?php
/**
 * @var \App\Model\Entity\Project $project
 * @var int $statusId
 */
use App\Model\Entity\Project;

// @codingStandardsIgnoreStart
$body = match ($statusId) {
    Project::STATUS_UNDER_REVIEW => 'This will submit the project for review and prevent further updates from the applicant.',
    Project::STATUS_ACCEPTED => 'This will send a congratulations message to the applicant and allow this project to be voted on.',
    Project::STATUS_REJECTED => 'This will send a message to the applicant and not allow this project to be voted on. Provide the reason why this application cannot be accepted.',
    Project::STATUS_REVISION_REQUESTED => 'Provide a specific request for what needs to be added or changed in this application before it can be accepted.',
    Project::STATUS_AWARDED_NOT_YET_DISBURSED => 'This will send a congratulations message to the applicant telling them that they\'ve been awarded funding pending the submission of a loan agreement. Only submit this change if the voting results for this cycle support awarding funding, and enter the amount of funding that will be awarded.',
    Project::STATUS_AWARDED_AND_DISBURSED => 'This will mark this project as having had funding disbursed. <strong>Only make this change if a check for ' .  $project->amount_awarded_formatted . ' has been written and sent to the applicant.</strong>',
    Project::STATUS_NOT_AWARDED => 'This will send a consolation message to the applicant telling them that this cycle\'s voting and/or the available budget did not result in their project receiving funding.',
    Project::STATUS_WITHDRAWN => 'This will withdraw this application from consideration. The applicant will not be automatically notified of this.',
    default => 'Uh oh. Unrecognized status ID: ' . $statusId,
}
// @codingStandardsIgnoreEnd
?>


<div class="modal fade" id="review-modal-<?= $statusId ?>" tabindex="-1"
     aria-labelledby="review-modal-<?= $statusId ?>-label" aria-hidden="true">
    <form method="post">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="review-modal-<?= $statusId ?>-label">
                        Update project status
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>
                        <?= $body ?>
                    </p>

                    <?php if (in_array($statusId, Project::getStatusesNeedingMessages())): ?>
                        <div class="form-group required">
                            <label for="message-<?= $statusId ?>">
                                Message to applicant:
                            </label>
                            <textarea name="message" class="form-control" rows="5" required="required" id="message-<?= $statusId ?>"></textarea>
                        </div>
                    <?php endif; ?>

                    <?php if ($statusId == Project::STATUS_AWARDED_NOT_YET_DISBURSED): ?>
                        <div class="form-group">
                            <label>
                                Amount to award
                                <br />
                                <input class="form-control" type="number" name="amount_awarded" required="required"
                                       data-validity-message="Required"
                                       value="<?= $project->amount_awarded ?>"
                                       max="<?= $project->amount_requested ?>"
                                       min="<?= $project->accept_partial_payout ? 1 : $project->amount_requested ?>">
                            </label>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="status_id" value="<?= $statusId ?>" />
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <?= Project::getStatusAction($statusId)['label'] ?>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
