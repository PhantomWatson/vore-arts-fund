<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var string $supportEmail
 */
?>
<p>
    <?= $user->name ?>,
</p>

<p>
    Your <a href="https://voreartsfund.org">Vore Arts Fund</a> password has been successfully changed.
</p>
<p>
    If you did not ask for your password to be changed, please contact us immediately at
    <a href="mailto:<?= $supportEmail ?>"><?= $supportEmail ?></a>.
</p>
