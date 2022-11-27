<?php
/**
 * @var \App\View\AppView $this
 */
$supportEmail = \Cake\Core\Configure::read('supportEmail');
?>

<p>
    If you have a <strong>question</strong> or <strong>feedback</strong> about the Vore Arts Fund
    or would like to report a <strong>problem with the website</strong>, please email
    <a href="mailto:<?= $supportEmail ?>"><?= $supportEmail ?></a>.
</p>

<p>
    <strong>Written correspondence</strong> can be sent to PO Box 1604, Muncie, IN, 47308.
</p>

<p>
    If you're <strong>seeking a loan</strong> to support your commercial art project, you can
    <?= $this->Html->link(
        'apply for funding through our website',
        [
            'controller' => 'Applications',
            'action' => 'apply',
        ],
    ) ?>.
</p>
