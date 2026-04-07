<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Report[]|\Cake\Collection\CollectionInterface $reports
 */
?>
<?php if ($reports): ?>
    <?php foreach ($reports as $report): ?>
        <?= $this->element('report', compact('report')) ?>
    <?php endforeach; ?>
<?php else: ?>
    <p class="alert alert-info">
        This project has had no updates submitted for it yet.
    </p>
<?php endif; ?>
