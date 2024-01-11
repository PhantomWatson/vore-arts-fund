<?php
/**
 * @var \App\View\AppView $this
 * @var bool $hasVoted
 * @var \App\Model\Entity\FundingCycle|null $votingCycle
 * @var int|null $votingProjectCount
 */
?>

<?php if ($votingCycle): ?>
    <div class="row">
        <div class="col">
            <section class="card">
                <div class="card-body">
                    <h1>
                        Vote
                    </h1>
                    <?php if ($hasVoted): ?>
                        <p>
                            Thanks for casting your votes! Be sure to tell all of your friends to sign up and help us
                            decide how to award funding before the deadline on
                            <?= $votingCycle->vote_end_local->i18nFormat('MMM d, YYYY') ?>.
                        </p>
                    <?php else: ?>
                        <p>
                            Voting is currently underway for the
                            <?= $votingProjectCount ?>
                            <?= __n('project', 'projects', $votingProjectCount) ?>
                            in the
                            <?= $votingCycle->name ?> funding cycle, and the deadline to cast your votes is
                            <?= $votingCycle->vote_end_local->i18nFormat('MMM d, YYYY') ?>.
                        </p>
                        <p>
                            <?= $this->Html->link(
                                'Cast your votes',
                                ['controller' => 'Votes', 'action' => 'index', 'prefix' => false],
                                ['class' => 'btn btn-primary']
                            ) ?>
                        </p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
<?php endif; ?>
