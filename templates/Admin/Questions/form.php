<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Question $question
 */
?>

<?= $this->Form->create($question) ?>
<fieldset>
    <?php
        echo $this->Form->control('question');
        echo $this->Form->control('enabled');
        echo $this->Form->control('weight');
    ?>
</fieldset>
<?= $this->Form->submit(__('Submit'), ['class' => 'btn btn-primary']) ?>
<?= $this->Form->end() ?>
