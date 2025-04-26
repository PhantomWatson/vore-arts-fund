<?php
/**
 * @var \App\View\AppView $this
 * @var bool $fundingCycleHasProjects
 * @var int|null $projectsCount
 * @var bool $canVote
 * @var \App\Model\Entity\FundingCycle $cycle
 * @var bool $showUpcoming
 * @var \App\Model\Entity\FundingCycle|null $nextCycle
 */
?>
<div class="alert alert-info">
    <p>
        To better bridge the gap between the public, working artists, and nonprofits, the Vore Arts Fund makes its funding decisions through a public vote. In every funding cycle, anyone can submit a ranked-choice voting ballot to tell us how to prioritize all eligible applications. Then, we'll spend that cycle's budget funding as many projects as we can, and in the order determined by those votes.
    </p>

    <?php if ($fundingCycleHasProjects && !$canVote): ?>
        <p>
            <?php
                $projects = ($projectsCount ?? 0) ? __n('project', "$projectsCount projects", $projectsCount) : 'projects';
                echo $this->Html->link(
                    "Learn about the $projects available to vote on",
                    [
                        'prefix' => false,
                        'controller' => 'Projects',
                        'action' => 'index',
                        '#' => 'projects-for-cycle-' . $cycle->id,
                    ]
                );
            ?>
        </p>
    <?php endif; ?>

    <?php if ($showUpcoming): ?>
        <p>
            <?php if ($nextCycle): ?>
                Voting for projects in the <?= $nextCycle->name ?> funding cycle begins on
                <strong><?= $nextCycle->vote_begin_local->format('F j, Y') ?></strong>. See you then!
            <?php else: ?>
                Check back later for information about when voting will begin for the next funding cycle.
            <?php endif; ?>
        </p>
    <?php endif; ?>

    <?php if ($canVote): ?>
        <p>
            Voting is underway for the applicants in this funding cycle! Here are the steps:
        </p>
        <ol>
            <li>
                <strong>Select</strong> all of the applications that you think should be funded.
            </li>
            <li>
                <strong>Rank</strong> those applications from highest-priority to lowest-priority.
            </li>
            <li>
                <strong>Submit</strong> your vote!
            </li>
        </ol>
        <p>
            The deadline to cast your votes is
            <strong><?= $cycle->vote_end_local->format('F j, Y') ?></strong>.
        </p>
    <?php endif; ?>
</div>
