<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Article $article
 * @var bool|null $isLatest
 */
?>

<article class="article">
    <?php if ($isLatest ?? false): ?>
        <p>
            <span class="badge text-bg-secondary article__latest">Latest news</span>
        </p>
    <?php endif; ?>
    <h2 <?= $this->getRequest()->getParam('action') == 'view' ? 'class="visually-hidden"' : '' ?>>
        <?= $this->Html->link($article->title, ['action' => 'view', 'slug' => $article->slug]) ?>
    </h2>
    <p class="date">
        <?= $article->dated->format('F j, Y') ?>
    </p>
    <div class="body">
        <?= $article->formatted_body ?>
    </div>
</article>
