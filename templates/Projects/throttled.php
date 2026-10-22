<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Project $project
 */
?>

<p>
    We hate to be the bearer of bad news, but our
    <?= $this->Html->link(
        'eligibility requirements',
        ['controller' => 'Pages', 'action' => 'terms', '#' => 'applicant-eligibility'],
    ) ?>
    require that before you apply for another loan, you must either
</p>
<ul>
    <li>
        <?= $this->Html->link(
            'repay the full balance',
            [
                'prefix' => 'My',
                'controller' => 'Loans',
                'action' => 'payment',
                'id' => $project->id,
            ],
        ) ?>
        of your current loan,
    </li>
    <li>
        or wait one year from the date that your last loan was awarded
        (<?= $project->loan_awarded_date->format('F j, Y') ?>)
    </li>
</ul>
