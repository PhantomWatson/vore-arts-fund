<?php
/**
 * @var \App\Model\Entity\Application $application
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
                <?= $application->user->name ?>
            </td>
        </tr>
        <tr>
            <th>
                Category
            </th>
            <td>
                <?= $application->category->name ?>
            </td>
        </tr>
        <tr>
            <th>
                Funding cycle
            </th>
            <td>
                <?= $this->element('FundingCycles/link', ['fundingCycle' => $application->funding_cycle]) ?>
            </td>
        </tr>
        <tr>
            <th>
                Amount requested
            </th>
            <td>
                $<?= number_format($application->amount_requested) ?>
            </td>
        </tr>
    </tbody>
</table>

<?php if ($application->images): ?>
    <section class="application-view">
        <h3 class="visually-hidden">
            Images
        </h3>
        <div class="image-gallery">
            <?php foreach ($application->images as $image): ?>
                <?= $this->Image->thumb($image) ?>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<section class="application-view">
    <h3>
        Description
    </h3>
    <p>
        <?= nl2br($application->description) ?>
    </p>
</section>

<?php foreach ($questions as $question): ?>
    <?php
        $answer = Hash::filter($application->answers, function ($answer) use ($question) {
            return $answer->question_id == $question->id;
        });
    ?>
    <section class="application-view">
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
