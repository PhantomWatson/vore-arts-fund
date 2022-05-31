<?php
/**
 * @var \App\Model\Entity\Application[]|\Cake\ORM\ResultSet $applications
 * @var \App\View\AppView $this
 */
?>
<?= $this->title() ?>

<?php if ($applications->count()): ?>
    <table class="table">
        <thead>
            <tr>
                <th>
                    Funding Cycle
                </th>
                <th>
                    Status
                </th>
                <th>
                    Actions
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($applications as $application): ?>
                <tr>
                    <td>
                        <?= $application->funding_cycle->name ?>
                    </td>
                    <td>
                        <?= $application->status_name ?>
                    </td>
                    <td>
                        <?= $this->Html->link(
                            'Update',
                            [
                                'controller' => 'Applications',
                                'action' => 'edit',
                                'id' => $application->id,
                            ],
                            ['class' => 'btn btn-secondary']
                        ) ?>
                        <?= $this->Html->link(
                            'Submit',
                            [
                                'controller' => 'Applications',
                                'action' => 'submit',
                                'id' => $application->id,
                            ],
                            ['class' => 'btn btn-secondary']
                        ) ?>
                        <?= $this->Html->link(
                            'Withdraw',
                            [
                                'controller' => 'Applications',
                                'action' => 'withdraw',
                                'id' => $application->id,
                            ],
                            ['class' => 'btn btn-secondary']
                        ) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class="alert alert-info">
        You have not yet submitted any applications for funding.
    </p>
<?php endif; ?>
