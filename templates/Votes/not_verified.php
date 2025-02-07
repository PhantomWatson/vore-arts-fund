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
    Before you can vote, you must first
    <?= $this->Html->link('verify your phone number', ['controller' => 'Users', 'action' => 'verify']) ?>.
</p>
