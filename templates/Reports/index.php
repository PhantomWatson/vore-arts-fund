<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Report>|\Cake\ORM\ResultSet $reports
 */
?>
<div class="reports">
    <?php if (count($reports) === 0): ?>
        <p class="alert alert-info">
            No reports have been submitted for any applications yet.
        </p>
    <?php else: ?>
        <?php foreach ($reports as $report): ?>
            <?= $this->element('report', compact('report')) ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
