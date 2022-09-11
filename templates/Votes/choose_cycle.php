<?php
/**
 * @var \Cake\ORM\ResultSet|\App\Model\Entity\FundingCycle[] $cyclesCurrentlyVoting
 */
?>

<p>
    Multiple funding cycles are currently accepting votes. Please select one to continue:
</p>
<ul>
    <?php foreach ($cyclesCurrentlyVoting as $c): ?>
        <li>
            <?= $this->Html->link(
                $c->name,
                [
                    'controller' => 'Votes',
                    'action' => 'index',
                    $c->id,
                ]
            ) ?>
        </li>
    <?php endforeach; ?>
</ul>
