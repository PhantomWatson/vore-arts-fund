<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Article[] $articles
 */
?>
<p class="news-intro">
    <?= $this->Html->link(
        'Sign up to our mailing list',
        ['controller' => 'MailingList', 'action' => 'signup'],
    ) ?> to stay up-to-date on new developments from the Vore Arts Fund,
    opportunities to support our mission, and announcements about applying for funding and voting on
    applications.
</p>

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
