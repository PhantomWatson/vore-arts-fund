<?php
/**
 * @var \App\Model\Entity\Project $project
 * @var \App\Model\Entity\Question[] $questions
 * @var \App\View\AppView $this
 * @var string|null $back
 */
$back = $back ?? null;

$this->Html->css('/viewerjs/viewer.min.css', ['block' => true]);
?>

<p>
    <?php
        $startsWithVowel = in_array(substr($project->category->name, 0, 1), ['a', 'e', 'i', 'o', 'u']);
        switch ($project->category->name) {
            case 'Other':
                echo 'A miscellaneous';
                break;
            default:
                echo ($startsWithVowel ? 'An ' : 'A ') . lcfirst($project->category->name);
        }
    ?>
    project by <?= $project->user->name ?>, who is requesting <?= strtolower($project->amount_requested_formatted) ?>
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
