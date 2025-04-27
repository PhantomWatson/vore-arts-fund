<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Article $article
 */
?>
<div class="article-form">
    <?= $this->Form->create($article) ?>
    <?php
        echo $this->Form->control('title');
        echo $this->Form->control('body', ['type' => 'textarea']);
    ?>
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
    <?php if (!$article->isNew()): ?>
        <p>
            <?= $this->Form->postLink('Delete', ['action' => 'delete', $article->id], [
                'confirm' => 'Are you sure you want to delete this article? No takesies backsies.',
                'class' => 'btn btn-danger'
            ]) ?>
        </p>
    <?php endif; ?>
</div>
