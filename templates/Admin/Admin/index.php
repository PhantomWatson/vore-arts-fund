<?php
/**
 * @var \App\Model\Entity\Project[] $projects
 * @var \App\View\AppView $this
 */
$controllers = [
    'Articles' => 'News Articles',
    'FundingCycles' => 'Funding Cycles',
    'Loans' => 'Loans',
    'Projects' => 'Projects',
    'Questions' => 'Questions',
    'Transactions' => 'Transactions',
    'Votes' => 'Awarding',
];
asort($controllers);
?>

<ul>
    <?php foreach ($controllers as $controller => $label): ?>
        <li>
            <?= $this->Html->link($label, [
                'prefix' => 'Admin',
                'controller' => $controller,
                'action' => 'index',
            ]) ?>
        </li>
    <?php endforeach; ?>
</ul>

