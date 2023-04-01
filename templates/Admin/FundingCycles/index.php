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
    <section>
        <h2>
            <?= ucwords($group) ?>
        </h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Application Dates</th>
                    <th>Voting Dates</th>
                    <th>Funding Available</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fundingCycles[$group] as $fundingCycle): ?>
                    <tr>
                        <td><?= sprintf(
                                '%s to %s',
                                $fundingCycle['application_begin']->i18nFormat($dateFormat),
                                $fundingCycle['application_end']->i18nFormat($dateFormat)
                            ) ?></td>
                        <td><?= sprintf(
                                '%s to %s',
                                $fundingCycle['vote_begin']->i18nFormat($dateFormat),
                                $fundingCycle['vote_end']->i18nFormat($dateFormat)
                            ) ?></td>
                        <td>
                            $<?= number_format($fundingCycle['funding_available']) ?>
                        </td>
                        <td>
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
