<?php
/**
 * @var \App\Model\Entity\FundingCycle[] $fundingCycles
 * @var string $title
 */
?>

<?= $this->element('title', ['title' => $title]) ?>

<?php if ($fundingCycles): ?>
    <p>
        Recipients are decided and funding is disbursed shortly after each funding cycle's voting deadline.
    </p>
    <?php foreach ($fundingCycles as $fundingCycle): ?>
        <section>
            <h2>
                <?= $fundingCycle->name ?>
            </h2>
            <table class="table">
                <tbody>
                    <tr>
                        <th>
                            Applications accepted
                        </th>
                        <td>
                            <?= $fundingCycle->application_begin->format('F j, Y') ?>
                            to
                            <?= $fundingCycle->application_end->format('F j, Y') ?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Voting
                        </th>
                        <td>
                            <?= $fundingCycle->vote_begin->format('F j, Y') ?>
                            to
                            <?= $fundingCycle->vote_end->format('F j, Y') ?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Budget
                        </th>
                        <td>
                            <?=
                                $fundingCycle->funding_available
                                    ? '$' . number_format($fundingCycle->funding_available)
                                    : 'Not yet determined'
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>
    <?php endforeach; ?>
<?php else: ?>
    <?= $this->element('no_funding_cycle') ?>
<?php endif; ?>
