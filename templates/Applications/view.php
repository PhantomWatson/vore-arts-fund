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
<?= $this->title() ?>

<?php if ($back): ?>
    <?= $this->Html->link(
        'Back',
        $back,
        ['class' => 'btn btn-secondary']
    ) ?>
<?php endif; ?>

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
        <div id="image-gallery">
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
    <section class="application-view">
        <h3>
            <?= $question->question ?>
        </h3>
        <p>
            <?php
                $answer = Hash::filter($application->answers, function ($answer) use ($question) {
                    return $answer->question_id == $question->id;
                });
                $answer = current($answer);
                echo $answer->answer;
            ?>
        </p>
    </section>
<?php endforeach; ?>

<script src="/viewerjs/viewer.min.js"></script>
<script type="text/javascript">
    const gallery = new Viewer(
        document.getElementById('image-gallery'),
        {
            url: 'data-full',
            toolbar: {
                zoomIn: true,
                zoomOut: true,
                oneToOne: false,
                reset: true,
                prev: true,
                play: false,
                next: true,
                rotateLeft: false,
                rotateRight: false,
                flipHorizontal: false,
                flipVertical: false,
            },
        }
    );
</script>
