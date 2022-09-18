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

<?php if ($back): ?>
    <?= $this->Html->link(
        'Back',
        $back,
        ['class' => 'btn btn-secondary']
    ) ?>
<?php endif; ?>

<p>
    <strong>Applicant:</strong> <?= $application->user->name ?>
</p>
<p>
    <strong>Category:</strong> <?= $application->category->name ?>
</p>
<p>
    <strong>Funding cycle:</strong> <?= $application->funding_cycle->name ?>
</p>

<?php if ($application->images): ?>
    <section class="application-view">
        <h3 class="visually-hidden">
            Images
        </h3>
        <div class="image-gallery">
            <?php foreach ($application->images as $image): ?>
                <img src="/img/applications/<?= Image::THUMB_PREFIX ?><?= $image->filename ?>"
                     alt="<?= $image->caption ?>" class="img-thumbnail" title="Click to open full-size image"
                     data-full="/img/applications/<?= $image->filename ?>" />
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

<script src="/viewerjs/viewer.min.js"></script>
<script src="/js/image-viewer.js"></script>
