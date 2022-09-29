<?php
/**
 * @var \App\Model\Entity\User $user
 * @var \App\View\AppView $this
 * @var string $url
 */
?>

<p>
    <?= $user->name ?>,
</p>

<p>
    You may change your password using the link below.
</p>

<p>
    <a href="<?= $url ?>">
        <?= $url ?>
    </a>
</p>

<p>
    Your password won't change until you access the link above and create a new one.
</p>
