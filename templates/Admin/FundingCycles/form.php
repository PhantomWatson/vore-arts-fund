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

<?= $this->title() ?>

<?= $this->Flash->render() ?>

<p class="alert alert-info">
    Funding cycles should begin on midnight of their first day and end on 11:59pm of their last day.
</p>

<?= $this->Form->create($fundingCycle) ?>
<fieldset>
    <?= $this->Form->control('application_begin') ?>
    <?= $this->Form->control('application_end') ?>
    <?= $this->Form->control('resubmit_deadline') ?>
    <?= $this->Form->control('vote_begin') ?>
    <?= $this->Form->control('vote_end') ?>
    <?= $this->Form->control('funding_available') ?>
</fieldset>
<?= $this->Form->submit(__('Submit'), ['class' => 'btn btn-primary']) ?>
<?= $this->Form->end() ?>
