<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Article[] $articles
 */
?>
<div class="articles">
    <?php if (count($articles) === 0): ?>
        <p class="alert alert-info">
            Check back later for updates about the Vore Arts Fund!
        </p>
    <?php else: ?>
        <?= $this->element('pagination') ?>

        <?php foreach ($articles as $article): ?>
            <?= $this->element('Articles/article', ['article' => $article]) ?>
        <?php endforeach; ?>

        <?= $this->element('pagination') ?>
    <?php endif; ?>
</div>
