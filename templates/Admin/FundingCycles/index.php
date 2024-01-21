<?php
/**
 * @var \App\Model\Entity\FundingCycle[]|\Cake\ORM\ResultSet $fundingCycles
 */

$dateFormat = 'MMM d, YYYY';
?>

<p>
    <?= $this->Html->link(
        'Add',
        [
            'prefix' => 'Admin',
            'controller' => 'FundingCycles',
            'action' => 'add',
        ],
        ['class' => 'btn btn-primary']
    ) ?>
</p>

<?php foreach (['current', 'future', 'past'] as $group): ?>
    <?php if (!count($fundingCycles[$group])) continue; ?>
    <section class="funding-cycles">
        <h2>
            <?= ucwords($group) ?>
        </h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Cycle</th>
                    <th>Application Period</th>
                    <th>Voting Period</th>
                    <th>Funding Available</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fundingCycles[$group] as $fundingCycle): ?>
                    <tr>
                        <td>
                            <?= $fundingCycle->name ?>
                        </td>
                        <td>
                            <?= sprintf(
                                '%s to %s',
                                $fundingCycle->application_begin_local->i18nFormat($dateFormat),
                                $fundingCycle->application_end_local->i18nFormat($dateFormat)
                            ) ?>
                        </td>
                        <td>
                            <?= sprintf(
                                '%s to %s',
                                $fundingCycle->vote_begin_local->i18nFormat($dateFormat),
                                $fundingCycle->vote_end_local->i18nFormat($dateFormat)
                            ) ?>
                        </td>
                        <td>
                            <?= $fundingCycle->funding_available_formatted ?>
                        </td>
                        <td>
                            <?php if ($fundingCycle->projects[0]->count): ?>
                                <?= $this->Html->link(
                                    $fundingCycle->projects[0]->count
                                    . __n(' Project', ' Projects', $fundingCycle->projects[0]->count),
                                    [
                                        'prefix' => 'Admin',
                                        'controller' => 'FundingCycles',
                                        'action' => 'projects',
                                        $fundingCycle['id'],
                                    ],
                                    ['class' => 'btn btn-secondary']
                                ) ?>
                            <?php else: ?>

                            <?php endif; ?>
                            <?= $this->Html->link(
                                'Edit',
                                [
                                    'prefix' => 'Admin',
                                    'controller' => 'FundingCycles',
                                    'action' => 'edit',
                                    'id' => $fundingCycle['id'],
                                ],
                                ['class' => 'btn btn-secondary']
                            ) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
<?php endforeach; ?>
