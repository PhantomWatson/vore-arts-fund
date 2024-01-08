<?php
/**
 * @var array|string $url
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
                    <option value="<?= $fundingCycle->id ?>"
                        <?= $fundingCycleId ?? null == $fundingCycle->id ? 'selected' : null ?>
                    >
                        #<?= $fundingCycle->id ?>: <?= $fundingCycle->name ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span id="loading-indicator" style="display: none;">
                    <i class="fa-solid fa-spinner fa-spin-pulse" title="Loading"></i>
                </span>
        </label>
    </p>

    <script>
        const selector = document.getElementById('funding-cycle-selector');
        selector.addEventListener('change', (event) => {
            const fundingCycleId = event.target.value;
            document.getElementById('loading-indicator').style.display = 'inline';
            document.location = '<?= \Cake\Routing\Router::url($url) ?>/' + fundingCycleId;
        });
    </script>
<?php endif; ?>
