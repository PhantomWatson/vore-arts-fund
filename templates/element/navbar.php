<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
$loggedIn = (bool)$user;
$isAdmin = $user->is_admin ?? false;
$isVerified = $user->is_verified ?? false;
?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #BA0C2F">
    <a class="navbar-brand" href="/">Vore Arts Fund</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <?= $this->Html->link('Home', '/', ['class' => 'nav-link']) ?>
            </li>
            <li class="nav-item">
                <?= $this->Html->linkFromPath('Vote', 'Votes::index', [], ['class' => 'nav-link']) ?>
            </li>
            <?php if ($loggedIn): ?>
                <li class="nav-item">
                    <?= $this->Html->linkFromPath('Account', 'Users::myAccount', [], ['class' => 'nav-link']) ?>
                </li>
                <li class="nav-item">
                    <?= $this->Html->linkFromPath('Apply', 'Applications::apply', [], ['class' => 'nav-link']) ?>
                </li>
                <li class="nav-item">
                    <?= $this->Html->linkFromPath('Log Out', 'Users::logout', [], ['class' => 'nav-link']) ?>
                </li>
                <?php if ($isAdmin): ?>
                    <li class="nav-item">
                        <?= $this->Html->linkFromPath('Admin', 'Admin::index', [], ['class' => 'nav-link']) ?>
                    </li>
                <?php endif; ?>
                <?php if (!$isVerified): ?>
                    <li class="nav-item">
                        <?= $this->Html->linkFromPath('Verify', 'Users::verify', [], ['class' => 'nav-link']) ?>
                    </li>
                <?php endif; ?>
            <?php else: ?>
                <li class="nav-item">
                    <?= $this->Html->linkFromPath('Register', 'Users::register', [], ['class' => 'nav-link']) ?>
                </li>
                <li class="nav-item">
                    <?= $this->Html->linkFromPath('Login', 'Users::login', [], ['class' => 'nav-link']) ?>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
