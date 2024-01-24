<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\FundingCycle $fundingCycle
 */
?>

<div class="card">
    <div class="card-body">
        <p>
            Funding cycles should begin on midnight of their first day and end on 11:59pm of their last day. If resubmitting
            a rejected application is allowed, the resubmit deadline should be set to sometime between the application
            deadline and the beginning of voting.
        </p>

        <table class="table table-bordered" id="cycles-chart">
            <thead>
            <tr>
                <th>Cycle</th>
                <?php foreach (explode(' ', 'Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec') as $month): ?>
                    <th><?= $month ?></th>
                <?php endforeach; ?>
            </tr>
            </thead>
            <tbody>
            <tr class="spring">
                <th>Spring</th>
                <td>App</td>
                <td>Rev</td>
                <td>Vote</td>
                <td>Disb</td>
                <td colspan="6" class="blank"></td>
                <td colspan="2">App</td>
            </tr>
            <tr class="summer">
                <th>Summer</th>
                <td class="blank"></td>
                <td colspan="3">App</td>
                <td>Rev</td>
                <td>Vote</td>
                <td>Disb</td>
                <td colspan="5" class="blank"></td>
            </tr>
            <tr class="fall">
                <th>Fall</th>
                <td colspan="4" class="blank"></td>
                <td colspan="3">App</td>
                <td>Rev</td>
                <td>Vote</td>
                <td>Disb</td>
                <td colspan="2" class="blank"></td>
            </tr>
            <tr class="winter">
                <th>Winter</th>
                <td>Disb</td>
                <td colspan="6" class="blank"></td>
                <td colspan="3">App</td>
                <td>Rev</td>
                <td>Vote</td>
            </tr>
            </tbody>
        </table>

        <p>
            <label for="wizard-checkbox">
                <input type="checkbox" id="wizard-checkbox" /> Use wizard
            </label>
        </p>

        <div id="wizard-container" style="display: none;">
            <form id="wizard-form" class="row row-cols-lg-auto align-items-center">
                <div class="col-12">
                    <select id="wizard-year" class="form-control">
                        <option value="">
                            Year
                        </option>
                        <?php for ($year = date('Y'); $year <= date('Y') + 2; $year++): ?>
                            <option value="<?= $year ?>">
                                <?= $year ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-12">
                    <select id="wizard-quarter" class="form-control">
                        <option value="">
                            Quarter
                        </option>
                        <option value="1">
                            Spring
                        </option>
                        <option value="2">
                            Summer
                        </option>
                        <option value="3">
                            Fall
                        </option>
                        <option value="4">
                            Winter
                        </option>
                    </select>
                </div>
                <div class="col-12">
                    <input type="submit" class="btn btn-primary" value="Set dates" />
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->Form->create($fundingCycle, ['id' => 'funding-cycle-form']) ?>
<fieldset>
    <?= $this->Form->control('application_begin') ?>
    <?= $this->Form->control('application_end') ?>
    <?= $this->Form->control('resubmit_deadline') ?>
    <?= $this->Form->control('vote_begin') ?>
    <?= $this->Form->control('vote_end') ?>
    <?= $this->Form->control('funding_available', ['label' => 'Funding available (in dollars)']) ?>
</fieldset>
<button type="submit" class="btn btn-primary">
    Submit
</button>
<?= $this->Form->end() ?>

<script>
    preventMultipleSubmit('#funding-cycle-form');

    const checkbox = document.getElementById('wizard-checkbox');
    const container = document.getElementById('wizard-container');
    const form = document.getElementById('wizard-form');

    const yearSelector = document.getElementById('wizard-year');
    const quarterSelector = document.getElementById('wizard-quarter');

    const applicationBegin = document.getElementById('application-begin');
    const applicationEnd = document.getElementById('application-end');
    const resubmitDeadline = document.getElementById('resubmit-deadline');
    const voteBegin = document.getElementById('vote-begin');
    const voteEnd = document.getElementById('vote-end');

    checkbox.addEventListener('change', (event) => {
        container.style.display = event.target.checked ? 'block' : 'none';
    });

    function setDateTime(input, month, year, startOrEnd) {
        let date = new Date(input.value);
        month = month - 1;
        date.setUTCFullYear(year, month);
        if (startOrEnd === 'start') {
            date.setUTCDate(1);
            date.setUTCHours(0, 0, 0);
        } else {
            date.setUTCDate((new Date(year, month + 1, 0)).getUTCDate());
            date.setUTCHours(23, 59, 59);
        }
        input.value = date.toISOString().slice(0, 19);
    }

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const year = yearSelector.value;
        const quarter = parseInt(quarterSelector.value);
        if (!year || !quarter) {
            alert('You have to select both a year and a quarter');
            return;
        }

        const applicationBeginMonth = ((quarter - 1) * 3) - 1;
        setDateTime(
            applicationBegin,
            quarter === 1 ? 11 : applicationBeginMonth,
            quarter === 1 ? year - 1 : year,
            'start'
        );
        const applicationEndMonth = applicationBeginMonth + 2;
        setDateTime(
            applicationEnd,
            quarter === 1 ? 1 : applicationEndMonth,
            year,
            'end'
        );
        setDateTime(
            resubmitDeadline,
            quarter === 1 ? 1 : applicationEndMonth,
            year,
            'end'
        );
        const voteBeginMonth = applicationBeginMonth + 4;
        setDateTime(
            voteBegin,
            quarter === 1 ? 3 : voteBeginMonth,
            year,
            'start'
        );
        setDateTime(
            voteEnd,
            quarter === 1 ? 3 : voteBeginMonth,
            year,
            'end'
        );
    });
</script>
