<?php
/**
 * @var \App\Model\Entity\Application[]|\Cake\ORM\ResultSet $applications
 * @var \App\Model\Entity\Status[] $statuses
 */
?>

<?= $this->title() ?>

<table>
    <thead>
        <tr>
            <th>Title</th>
            <th>Status</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($applications as $application): ?>
            <tr>
                <td><?= $application->title ?></td>
                <td><?= $application->status_name ?></td>
                <td><?= $this->Html->link(
                    'View',
                    [
                        'prefix' => 'Admin',
                        'controller' => 'Applications',
                        'action' => 'review',
                        'id' => $application['id'],
                    ],
                    ['class' => 'btn btn-secondary']
                ) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
