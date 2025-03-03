<?php
/**
 * @var \App\Model\Entity\Project $project
 * @var int $statusId
 * @var \App\View\AppView $this
 */
use App\Model\Entity\Project;

// @codingStandardsIgnoreStart
$body = match ($statusId) {
    Project::STATUS_UNDER_REVIEW => 'This will submit the project for review and prevent further updates from the applicant.',
    Project::STATUS_ACCEPTED => 'This will send a congratulations message to the applicant and allow this project to be voted on.',
    Project::STATUS_REJECTED => 'This will send a message to the applicant and not allow this project to be voted on. Provide the reason why this application cannot be accepted.',
    Project::STATUS_REVISION_REQUESTED => 'Provide a specific request for what needs to be added or changed in this application before it can be accepted.',
    Project::STATUS_AWARDED_NOT_YET_DISBURSED => 'This will send a congratulations message to the applicant telling them that they\'ve been awarded funding pending the submission of a loan agreement. Only submit this change if voting for this cycle has completed and if the results support awarding this project funding.',
    Project::STATUS_AWARDED_AND_DISBURSED => 'This will mark this project as having had funding disbursed. <strong>Only make this change if a check for ' .  $project->amount_awarded_formatted . ' has been written and sent to the applicant.</strong>',
    Project::STATUS_NOT_AWARDED => 'This will send a consolation message to the applicant telling them that this cycle\'s voting and/or the available budget did not result in their project receiving funding.',
    Project::STATUS_WITHDRAWN => 'This will withdraw this application from consideration. The applicant will not be automatically notified of this.',
    Project::STATUS_DELETED => 'This will soft-delete this application, essentially permanently hiding it. The applicant will not be notified.',
    default => 'Uh oh. Unrecognized status ID: ' . $statusId,
};
// @codingStandardsIgnoreEnd

$minAwardable = $project->accept_partial_payout ? 1 : $project->amount_requested;
$maxAwardable = min($project->amount_requested, $project->funding_cycle->funding_available);
$prefilledAwardAmount = $this->getRequest()->getQuery('amountAwarded');
$blockSubmitting = $statusId == Project::STATUS_AWARDED_NOT_YET_DISBURSED && !$prefilledAwardAmount;
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
                        <?php if ($prefilledAwardAmount): ?>
                            <div class="form-group">
                                <label>
                                    Amount to award
                                    <br />
                                    <input class="form-control" type="number" name="amount_awarded" required="required"
                                           data-validity-message="Required"
                                           value="<?= $prefilledAwardAmount ?>"
                                           max="<?= $maxAwardable ?>"
                                           min="<?= $minAwardable ?>"
                                           readonly="readonly"
                                    >
                                </label>
                            </div>
                        <?php else: ?>
                            <p>
                                We can award between $<?= number_format($minAwardable) ?> and $<?= number_format($maxAwardable) ?> to this project.
                                To award funding, go to
                                <?= $this->Html->link(
                                    'the voting page for this funding cycle',
                                    [
                                        'prefix' => 'Admin',
                                        'controller' => 'Votes',
                                        'action' => 'index',
                                        'id' => $project->funding_cycle_id,
                                    ]
                                ) ?>
                                and follow the link for this project that will send you back here with the awardable amount pre-filled.
                            </p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="status_id" value="<?= $statusId ?>" />
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <?php if (!$blockSubmitting): ?>
                        <button type="submit" class="btn btn-primary">
                            <?= Project::getStatusAction($statusId)['label'] ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </form>
</div>
