<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Article $article
 * @var string $rteJsPath
 */
if ($rteJsPath) {
    $this->Html->script($rteJsPath, ['block' => true, 'type' => 'module']);
}
?>
<div class="article-form">
    <?= $this->Form->create($article) ?>
    <?= $this->Form->control('title') ?>
    <div class="form-group text required">
        <label for="body" class="visually-hidden">Article body</label>
        <?= $this->Form->textarea('body', ['data-rte-target' => 1]) ?>
        <div id="rte-root"></div>
    </div>

    <?php
        echo $this->Form->control('dated', ['empty' => true]);
    ?>

    <div class="form-group article-form__is-published">
        <?= $this->Form->radio('is_published', [1 => 'Publish', 0 => 'Save as draft'], ['required' => true]) ?>
    </div>

    <div class="form-group">
        <?= $this->Form->button($article->isNew() ? 'Submit' : 'Update', ['class' => 'btn btn-primary']) ?>
    </div>

    <?= $this->Form->end() ?>

    <?php if (!$article->isNew()) : ?>
        <div class="form-group">
            <?= $this->Form->postLink(
                'Delete',
                ['action' => 'delete', $article->id],
                ['confirm' => 'Are you sure you want to delete this article? No takesies backsies.', 'class' => 'btn btn-danger']
            ) ?>
        </div>
    <?php endif; ?>
</div>
