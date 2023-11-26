<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Report $report
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Report'), ['action' => 'edit', $report->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Report'), ['action' => 'delete', $report->id], ['confirm' => __('Are you sure you want to delete # {0}?', $report->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Reports'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Report'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="reports view content">
            <h3><?= h($report->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('User') ?></th>
                    <td><?= $report->has('user') ? $this->Html->link($report->user->name, ['controller' => 'Users', 'action' => 'view', $report->user->id]) : '' ?></td>
                </tr>
                <tr>
                    <th>Project</th>
                    <td><?= $report->has('projects') ? $this->Html->link($report->projects->title, ['controller' => 'Projects', 'action' => 'view', $report->projects->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($report->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= $report->created->setTimezone(\App\Application::LOCAL_TIMEZONE) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= $report->modified->setTimezone(\App\Application::LOCAL_TIMEZONE) ?></td>
                </tr>
                <tr>
                    <th><?= __('Is Final') ?></th>
                    <td><?= $report->is_final ? __('Yes') : __('No'); ?></td>
                </tr>
            </table>
            <div class="text">
                <strong><?= __('Body') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($report->body)); ?>
                </blockquote>
            </div>
        </div>
    </div>
</div>
