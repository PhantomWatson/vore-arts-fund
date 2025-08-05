<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Project[] $pastProjects
 */
?>

<?php if ($pastProjects): ?>
    <p>
        If you'd like to resubmit a past application, select it below. You'll be able to make changes to the application before submitting it.
    </p>
    <table class="table">
        <thead>
            <tr>
                <th>Project</th>
                <th>Application date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pastProjects as $p): ?>
                <tr>
                    <td>
                        <?= $this->Html->link(
                            $p->title,
                            [
                                'controller' => 'Projects',
                                'action' => 'apply',
                                '?' => ['reapply' => $p->id]
                            ]
                        ) ?>
                    </td>
                    <td><?= $p->created->setTimezone(\App\Application::LOCAL_TIMEZONE)->format('F j, Y') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>
        We couldn't find any past applications of yours to resubmit. Would you like to
        <?= $this->Html->link(
            'start a new application',
            ['controller' => 'Projects', 'action' => 'apply']
        ) ?>?
    </p>
<?php endif; ?>
