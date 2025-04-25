<?php
/**
 * @var \App\Model\Entity\FundingCycle|null $cycle
 * @var \App\Model\Entity\FundingCycle|null $nextCycle
 * @var \App\View\AppView $this
 * @var array $toLoad
 * @var bool $canVote
 * @var bool $hasVoted
 * @var bool $isLoggedIn
 * @var bool $isVerified
 * @var bool $showUpcoming
 */
?>
<p class="alert alert-warning">
    Before you can vote, you must first verify your phone number.
    <?= $this->Html->link(
        'Verify',
        ['controller' => 'Users', 'action' => 'verify'],
        ['class' => 'btn btn-primary']
    ) ?>
</p>
