<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Article $article
 */
?>

<article class="article">
    <h1 <?= $this->getRequest()->getParam('action') == 'view' ? 'class="visually-hidden"' : '' ?>>
        <?= $this->Html->link($article->title, ['action' => 'view', 'slug' => $article->slug]) ?>
    </h1>
    <p>
        <?= $article->dated->format('F j, Y') ?>
    </p>
    <div class="body">
        <?= $article->formatted_body ?>
    </div>
</article>
