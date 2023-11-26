<?php
/**
 * @var \App\Model\Entity\Report $report
 */
?>
<article class="report bordered-section">
    <?php if ($report->is_final): ?>
        <h3 class="final">
            Final update
        </h3>
    <?php endif; ?>
    <h2>
        Project Update:
        <?= $report->projects->title ?>
    </h2>
    <p class="date">
        <?= $report->created->setTimezone(\App\Application::LOCAL_TIMEZONE)->format('F j, Y') ?>
        &nbsp; - &nbsp;
        <?= $this->Html->link(
            'Go to project',
            [
                'controller' => 'Projects',
                'action' => 'view',
                'id' => $report->projects->id,
            ],
        ) ?>
    </p>
    <div class="body">
        <?= nl2br($report->body) ?>
    </div>
</article>
