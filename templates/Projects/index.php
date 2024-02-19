<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\ORM\ResultSet|\App\Model\Entity\FundingCycle[] $fundingCycles
 * @var \Cake\ORM\ResultSet|Project[] $projects
 * @var bool $hasProjects
 */

use App\Model\Entity\Image;
use App\Model\Entity\Project;
$projectsWereDisplayed = false;
?>

<p>
    Commercial arts-related projects in Muncie, Indiana, are eligible to apply for loans through the Vore Arts Fund to
    cover their up-front costs. After every round of public voting, some projects get funded, and others can re-apply
    and try again in the following funding cycle. For more details, visit our
    <?= $this->Html->link(
        'about',
        ['controller' => 'Pages', 'action' => 'about']
    ) ?> page.
</p>

<?php if ($hasProjects): ?>
    <p>
        To view your own projects, go to
        <?= $this->Html->link(
            'Account > My Projects',
            ['prefix' => 'My', 'controller' => 'Projects', 'action' => 'index'],
        ) ?>.
    </p>
<?php endif; ?>

<?php if ($fundingCycles->isEmpty()): ?>
    <p>
        Please check back later for a list of projects that have applied for funding.
    </p>
<?php else: ?>
    <?php foreach ($fundingCycles as $fundingCycle): ?>
        <?php if (empty($fundingCycle->projects)) continue; ?>
        <?php $projectsWereDisplayed = true; ?>
        <section class="card projects-index__funding-cycle">
            <div class="card-header">
                <h1>
                    <?= $this->element(
                        'FundingCycles/link',
                        [
                            'fundingCycle' => $fundingCycle,
                            'append' => '',
                        ]
                    ) ?>
                </h1>
                <p>
                    Status:
                    <?= $fundingCycle->getStatusDescription(true) ?>
                </p>
            </div>
            <div class="card-body">
                <?php foreach ($fundingCycle->projects as $project): ?>
                    <article class="projects-index__project">
                        <h2 class="projects-index__project-title">
                            <?= $this->Html->link(
                                $project->title,
                                [
                                    'prefix' => false,
                                    'controller' => 'Projects',
                                    'action' => 'view',
                                    'id' => $project->id,
                                ]
                            ) ?>
                        </h2>
                        <p class="projects-index__project-details">
                            <?= $project->status_summary ?>
                            <?php if ($project->status_id == Project::STATUS_AWARDED): ?>
                                <br />
                                Awarded
                                <?= $project->amount_awarded
                                    ? ('$' . number_format($project->amount_awarded))
                                    : '(amount pending)'
                                ?>
                            <?php endif; ?>
                        </p>
                        <div class="row">
                            <div class="col-12 <?php if ($project->images): ?>col-sm-8<?php endif; ?>">
                                <?= $this->Text->truncate($project->description, 1000, ['exact' => false,]) ?>
                            </div>
                            <?php if ($project->images): ?>
                                <div class="projects-index__images col-12 col-sm-4">
                                    <?= sprintf(
                                        '<img src="/img/projects/%s%s" alt="%s" class="img-thumbnail" ' .
                                            'data-full="/img/projects/%s" />',
                                        Image::THUMB_PREFIX,
                                        $project->images[0]->filename,
                                        $project->images[0]->caption,
                                        $project->images[0]->filename,
                                    ) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>
    <?php if (!$projectsWereDisplayed): ?>
        <p>
            Please check back later for a list of projects that have applied for funding.
        </p>
    <?php endif; ?>
<?php endif; ?>
