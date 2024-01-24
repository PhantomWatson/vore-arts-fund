<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Question $question
 */
?>

<?= $this->Form->create($question, ['id' => 'question-form']) ?>
<fieldset>
    <?php
        echo $this->Form->control('question');
        echo $this->Form->control('enabled');
        echo $this->Form->control('weight');
    ?>
</fieldset>
<button type="submit" class="btn btn-primary">
    Submit
</button>
<?= $this->Form->end() ?>

<script>
    preventMultipleSubmit('#question-form');
</script>
