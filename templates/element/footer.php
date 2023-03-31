<?php
/**
 * @var \App\View\AppView $this
 */
$links = [
    'Contact' => 'contact',
    'Privacy policy' => 'privacy',
    'Terms of service' => 'terms',
]
?>
<footer>
    <p>
        &copy; <?= date('Y') ?> Vore Arts Fund Inc.
    </p>
    <ul>
        <?php foreach ($links as $label => $action): ?>
            <li>
                <?= $this->Html->link(
                    $label,
                    [
                        'prefix' => false,
                        'controller' => 'Pages',
                        'action' => $action,
                    ]
                ) ?>
            </li>
        <?php endforeach; ?>
    </ul>
</footer>
