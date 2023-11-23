<?php
/**
 * @var \App\Model\Entity\Project[]|\Cake\ORM\ResultSet $projects
 * @var \App\Model\Entity\FundingCycle[]|\Cake\ORM\ResultSet $fundingCycles
 * @var int $fundingCycleId
 */
?>

<?php if (count($projects)): ?>
    <table class="table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($projects as $project): ?>
                <tr>
                    <td><?= $project->title ?></td>
                    <td><?= $project->status_name ?></td>
                    <td><?= $this->Html->link(
                            'View',
                            [
                                'prefix' => 'Admin',
                                'controller' => 'Projects',
                                'action' => 'review',
                                'id' => $project['id'],
                            ],
                            ['class' => 'btn btn-secondary']
                        ) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class="alert alert-info">
        No applications have been received for this funding cycle
    </p>
<?php endif; ?>

<script>
    const selector = document.getElementById('funding-cycle-selector');
    selector.addEventListener('change', (event) => {
        const fundingCycleId = event.target.value;
        document.getElementById('loading-indicator').style.display = 'inline';
        document.location = '<?= \Cake\Routing\Router::url([
            'prefix' => 'Admin',
            'controller' => 'Projects',
            'action' => 'index',
        ]) ?>/' + fundingCycleId;
    });
</script>
