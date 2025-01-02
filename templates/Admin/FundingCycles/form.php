<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\FundingCycle $fundingCycle
 */
$fieldType = 'date';
?>

<div class="card">
    <div class="card-body">
        <p>
            All deadlines will be at 11:59pm on the specified dates.
            If resubmitting a rejected application is allowed, the resubmit deadline should be set to sometime
            between the application deadline and the beginning of voting.
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
                        <?php for ($year = date('Y'); $year <= (int)date('Y') + 2; $year++): ?>
                            <option value="<?= $year ?>">
                                <?= $year ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-12">
                    <select id="wizard-month" class="form-control">
                        <option value="">
                            Month when application period begins
                        </option>
                        <?php for ($month = 1; $month <= 12; $month++): ?>
                            <option value="<?= $month ?>">
                                <?= date('F', mktime(0, 0, 0, $month, 1)) ?>
                            </option>
                        <?php endfor; ?>
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
    <?= $this->Form->control('application_begin', ['type' => $fieldType, 'value' => $fundingCycle->application_begin_local]) ?>
    <?= $this->Form->control('application_end', ['type' => $fieldType, 'value' => $fundingCycle->application_end_local]) ?>
    <?= $this->Form->control('resubmit_deadline', ['type' => $fieldType, 'value' => $fundingCycle->resubmit_deadline_local]) ?>
    <?= $this->Form->control('vote_begin', ['type' => $fieldType, 'value' => $fundingCycle->vote_begin_local]) ?>
    <?= $this->Form->control('vote_end', ['type' => $fieldType, 'value' => $fundingCycle->vote_end_local]) ?>
    <?= $this->Form->control('funding_available', ['label' => 'Funding available (in dollars)']) ?>
    <?= $this->Form->control('is_finalized', ['label' => 'Is finalized (distribution has concluded)']) ?>
</fieldset>
<button type="submit" class="btn btn-primary">
    Submit
</button>
<?= $this->Form->end() ?>

<script src="/js/funding-cycle-wizard.js"></script>
