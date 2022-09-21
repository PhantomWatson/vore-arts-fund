<?php
/**
 * @var \App\Model\Entity\Application[]|\Cake\ORM\ResultSet $applications
 * @var \App\Model\Entity\FundingCycle[]|\Cake\ORM\ResultSet $fundingCycles
 * @var int $fundingCycleId
 */
?>

<?php if ($fundingCycles): ?>
    <p>
        <label for="funding-cycle-selector">
            Funding cycle:
            <select id="funding-cycle-selector" class="form-select">
                <?php foreach ($fundingCycles as $fundingCycle): ?>
                    <option value="<?= $fundingCycle->id ?>" <?= $fundingCycleId == $fundingCycle->id ? 'selected' : null ?>>
                        #<?= $fundingCycle->id ?>: <?= $fundingCycle->name ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span id="loading-indicator" style="display: none;">
                <i class="fa-solid fa-spinner fa-spin-pulse" title="Loading"></i>
            </span>
        </label>
    </p>

    <?php if (count($applications)): ?>
        <table class="table">
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
    <?php else: ?>
        <p class="alert alert-info">
            No applications have been received for this funding cycle
        </p>
    <?php endif; ?>
<?php else: ?>
    <p class="alert alert-info">
        No funding cycles found
    </p>
<?php endif; ?>

<script>
    const selector = document.getElementById('funding-cycle-selector');
    selector.addEventListener('change', (event) => {
        const fundingCycleId = event.target.value;
        document.getElementById('loading-indicator').style.display = 'inline';
        document.location = '<?= \Cake\Routing\Router::url([
            'prefix' => 'Admin',
            'controller' => 'Applications',
            'action' => 'index',
        ]) ?>/' + fundingCycleId;
    });
</script>
