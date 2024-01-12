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

<table class="table w-auto">
    <tbody>
        <?= $this->element('Projects/overview_public') ?>
    </tbody>
</table>

<?= $this->element('Projects/description') ?>

<?= $this->Image->initViewer() ?>
