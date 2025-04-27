<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Article[] $articles
 */
?>
<div class="admin-articles">
    <p>
        <?= $this->Html->link('Create new article', ['action' => 'add'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?php if (count($articles) === 0): ?>
        <p class="alert alert-info">
            No articles found
        </p>
    <?php else: ?>
        <?= $this->element('pagination') ?>

        <table class="table">
            <thead>
            <tr>
                <th>Article</th>
                <th>Date</th>
                <th>Author</th>
                <th>Published</th>
            </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $article): ?>
                    <tr>
                        <td>
                            <?= $this->Html->link($article->title, ['action' => 'edit', $article->id]) ?>
                        </td>
                        <td>
                            <?= $article->dated->format('F j, Y') ?>
                        </td>
                        <td>
                            <?= $article->user->name ?>
                        </td>
                        <td>
                            <?= $article->is_published ? '✔' : '❌' ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?= $this->element('pagination') ?>
    <?php endif; ?>
</div>
