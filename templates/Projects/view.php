<?php
/**
 * @var \App\Model\Entity\Project $project
 * @var \App\Model\Entity\Question[] $questions
 * @var \App\View\AppView $this
 * @var string|null $back
 */
$back = $back ?? null;

$this->Html->css('/viewerjs/viewer.min.css', ['block' => true]);
$isOwner = $this->getRequest()->getParam('prefix') == 'My';
?>

<?php if ($isOwner): ?>
    <p>
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Actions
            </button>

            <ul class="dropdown-menu">
                <?= $this->element('Projects/actions', compact('project')) ?>
            </ul>
        </div>
    </p>
<?php endif; ?>

<p>
    <?= $project->status_summary ?>
    in the
    <?= $this->element('FundingCycles/link', [
        'fundingCycle' => $project->funding_cycle,
        'append' => '',
    ]) ?>
    funding cycle
</p>

<?= $this->element('Projects/description') ?>

<?= $this->Image->initViewer() ?>

<?php if ($project->status_id == \App\Model\Entity\Project::STATUS_AWARDED): ?>
    <section class="card">
        <div class="card-body">
            <h1 class="card-title">
                Reports
            </h1>
            <p>
                <?php if (count($project->reports)): ?>
                    <?= $this->Html->link(
                        count($project->reports) . __n(' report', ' reports', count($project->reports)),
                        [
                            'prefix' => false,
                            'controller' => 'Reports',
                            'action' => 'project',
                            $project->id,
                        ]
                    ) ?>
                    <?= __n(' has', ' have', count($project->reports)) ?>
                    been submitted for this project
                <?php else: ?>
                    No reports have been submitted for this project yet
                <?php endif; ?>
            </p>
        </div>
    </section>
<?php endif; ?>
