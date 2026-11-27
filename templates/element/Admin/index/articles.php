<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\ORM\ResultSet<\App\Model\Entity\Report> $recentReports
 * @var \App\Model\Entity\Article|null $article
 */
?>

<div class="card">
    <div class="card-header">
        <h2>
            News articles
        </h2>
        <ul class="links">
            <li>
                <?= $this->Html->link('View all', [
                    'prefix' => 'Admin',
                    'controller' => 'Articles',
                    'action' => 'index',
                ]) ?>
            </li>
            <li>
                <?= $this->Html->link('Add a new article', [
                    'prefix' => 'Admin',
                    'controller' => 'Articles',
                    'action' => 'add',
                ]) ?>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <h3>
            Latest
        </h3>
        <div class="bordered-section">
            <?= $this->element('Articles/article', ['article' => $article]) ?>
        </div>
    </div>
</div>
