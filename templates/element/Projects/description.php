<?php
/**
 * @var \App\Model\Entity\Project $project
 * @var \App\Model\Entity\Question[] $questions
 * @var \App\View\AppView $this
 */

use Cake\Utility\Hash;

?>

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
