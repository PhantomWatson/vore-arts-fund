<?php
/**
 * @var \App\Model\Entity\Project $project
 * @var \App\Model\Entity\Question[] $questions
 * @var \App\View\AppView $this
 * @var string|null $back
 */
$back = $back ?? null;

use App\Model\Entity\Image;
use Cake\Utility\Hash;

$this->Html->css('/viewerjs/viewer.min.css', ['block' => true]);
?>

<table class="table w-auto">
    <tbody>
        <tr>
            <th>
                Applicant
            </th>
            <td>
                <?= $project->user->name ?>
            </td>
        </tr>
        <tr>
            <th>
                Category
            </th>
            <td>
                <?= $project->category->name ?>
            </td>
        </tr>
        <tr>
            <th>
                Funding cycle
            </th>
            <td>
                <?= $this->element('FundingCycles/link', ['fundingCycle' => $project->funding_cycle]) ?>
            </td>
        </tr>
        <tr>
            <th>
                Amount requested
            </th>
            <td>
                $<?= number_format($project->amount_requested) ?>
            </td>
        </tr>
        <tr>
            <th>
                Reports
            </th>
            <td>
                <?php if (count($project->reports)): ?>
                    <?= $this->Html->link(
                        count($project->reports) . ' (view)',
                        [
                            'prefix' => false,
                            'controller' => 'Reports',
                            'action' => 'projects',
                            $project->id,
                        ]
                    ) ?>
                <?php else: ?>
                    None
                <?php endif; ?>
            </td>
        </tr>
    </tbody>
</table>

<?php if ($project->images): ?>
    <section class="projects-view">
        <h3 class="visually-hidden">
            Images
        </h3>
        <div class="image-gallery">
            <?php foreach ($project->images as $image): ?>
                <?= $this->Image->thumb($image) ?>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<section class="projects-view">
    <h3>
        Description
    </h3>
    <p>
        <?= nl2br($project->description) ?>
    </p>
</section>

<?php foreach ($questions as $question): ?>
    <?php
        $answer = Hash::filter($project->answers, function ($answer) use ($question) {
            return $answer->question_id == $question->id;
        });
    ?>
    <section class="projects-view">
        <h3>
            <?= $question->question ?>
        </h3>
        <p class="<?= !$answer || !current($answer)->answer ? 'no-answer' : null ?>">
            <?php if ($answer): ?>
                <?= current($answer)->answer ?>
            <?php else: ?>
                No answer
            <?php endif; ?>
        </p>
    </section>
<?php endforeach; ?>

<?= $this->Image->initViewer() ?>
