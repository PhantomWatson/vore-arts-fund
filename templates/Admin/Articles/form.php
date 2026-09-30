<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Article $article
 * @var string $rteJsPath
 */

if ($rteJsPath) {
    $this->Html->script($rteJsPath, ['block' => true, 'type' => 'module']);
}

// After a failed submission, re-populate with the submitted images rather than the saved ones
$preloadImageData = array_map(function (\App\Model\Entity\ArticleImage|array $image) {
    return [
        'caption' => $image['caption'] ?? '',
        'filename' => $image['filename'] ?? null,
        'id' => $image['id'] ?? null,
        'weight' => $image['weight'] ?? null,
    ];
}, $this->getRequest()->getData('images') ?? $article->images ?? []);

?>
<div class="article-form">
    <?= $this->Form->create($article) ?>
    <fieldset>
        <legend>
            Article
        </legend>
        <?= $this->Form->control('title') ?>
        <div class="form-group text required">
            <label for="body" class="visually-hidden">Article body</label>
            <?= $this->Form->textarea('body', ['data-rte-target' => 1]) ?>
            <div id="rte-root"></div>
        </div>
    </fieldset>

    <fieldset>
        <legend>
            Images
        </legend>
        <div id="image-uploader-root"></div>
        <script>
            window.preloadImages = <?= json_encode($preloadImageData) ?>;
            window.imageUploaderConfig = {type: 'articles'};
        </script>
    </fieldset>

    <fieldset>
        <legend>
            Metadata
        </legend>

        <?= $this->Form->control('dated', ['empty' => true]) ?>

        <div class="form-group article-form__is-published">
            <?= $this->Form->radio('is_published', [1 => 'Publish', 0 => 'Save as draft'], ['required' => true]) ?>
        </div>
    </fieldset>

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

<?= $this->element('load_app_files') ?>
