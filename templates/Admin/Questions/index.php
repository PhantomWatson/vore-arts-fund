<?php
/**
 * @var \App\View\AppView $this
 * @var array $questions
 */
?>

<p>
    <?= $this->Html->link(__('Add Question'), ['action' => 'add'], ['class' => 'btn btn-primary']) ?>
</p>


<?php if ($questions['enabled']): ?>
    <section>
        <h2>
            Enabled
        </h2>
        <?= $this->element('Questions/table', ['questions' => $questions['enabled']]) ?>
    </section>
<?php else: ?>
    <p class="alert alert-info">
        There are no enabled questions
    </p>
<?php endif; ?>

<?php if ($questions['disabled']): ?>
    <section>
        <h2>
            Disabled
        </h2>
        <?= $this->element('Questions/table', ['questions' => $questions['enabled']]) ?>
    </section>
<?php endif; ?>
