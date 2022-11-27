<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Report> $reports
 */
?>
<?php if ($reports): ?>
    <?php foreach ($reports as $report): ?>
        <article class="report bordered-section">
            <h2>
                <?php if ($report->is_final): ?>
                    Project Final Report:
                <?php else: ?>
                    Project Update:
                <?php endif; ?>
                <?= $report->application->title ?>
            </h2>
            <p class="date">
                <?= $report->created->format('F j, Y') ?>
            </p>
            <div class="body">
                <?= nl2br($report->body) ?>
            </div>
        </article>
    <?php endforeach; ?>
<?php else: ?>
    <p class="alert alert-info">
        This project has had no updates submitted for it yet.
    </p>
<?php endif; ?>
