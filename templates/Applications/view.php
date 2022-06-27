<?php
/**
 * @var \App\Model\Entity\Application $application
 * @var \App\Model\Entity\Question[] $questions
 * @var \App\View\AppView $this
 * @var string|null $back
 */
$back = $back ?? null;

use Cake\Utility\Hash;
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

<?php if ($application->images): ?>
    <?php foreach ($application->images as $image): ?>
        <div class="card" style="width: 18rem;">
            <img src="/img/applications/<?= $image->filename ?>" class="card-img-top image-thumbnail" alt="<?= $image->caption ?>" />
            <div class="card-body">
                <p class="card-text">
                    <?= $image->caption ?>
                </p>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
