<?php
/**
 * @var \App\Model\Entity\User[] $users
 */
?>

<?php if ($users): ?>
    <table class="table">
        <thead>
            <tr>
                <th>
                    Recipient
                </th>
                <th>
                    Project(s)
                </th>
                <th>
                    Amount
                </th>
                <th>
                    Date awarded
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td rowspan="<?= count($user->projects) ?>">
                        <strong>
                            <?= $user->name ?>
                        </strong>
                    </td>
                    <td>
                        <?= $user->projects[0]->title ?>
                    </td>
                    <td>
                        <?= $user->projects[0]->amount_awarded_formatted ?>
                    </td>
                    <td>
                        <?= $user->projects[0]->loan_agreement_date_local->format('F j, Y') ?>
                    </td>
                </tr>
                <?php foreach (array_slice($user->projects, 1) as $project): ?>
                    <tr>
                        <td>
                            <?= $user->projects[0]->title ?>
                        </td>
                        <td>
                            <?= $user->projects[0]->amount_awarded_formatted ?>
                        </td>
                        <td>
                            <?= $user->projects[0]->loan_agreement_date_local->format('F j, Y') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class="alert alert-info">
        None found.
    </p>
<?php endif; ?>
