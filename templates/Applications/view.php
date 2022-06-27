<?php
/**
 * @var \App\Model\Entity\Application $application
 * @var \App\Model\Entity\Question[] $questions
 * @var \App\View\AppView $this
 */

use Cake\Utility\Hash;

?>
<?= $this->title() ?>

<p>
    <strong>Category:</strong> <?= $application->category->name ?>
</p>

<p>
    <strong>Funding cycle:</strong> <?= $application->funding_cycle->name ?>
</p>

<p>
    <?= nl2br($application->description) ?>
</p>

<?php foreach ($questions as $question): ?>
    <section class="application-view-q-a">
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

<?= $this->Html->link(
    'Back',
    [
        'controller' => 'Votes',
        'action' => 'index',
    ],
    ['class' => 'btn btn-secondary']
) ?>
