<?php
/**
 * @var \App\View\AppView $this
 * @var bool $hasApplications
 * @var bool $isAdmin
 * @var bool $isLoggedIn
 * @var bool $isVerified
 */
?>
<header id="header" class="fixed-top">
    <div class="container d-flex align-items-center justify-content-between">
        <h1 class="logo">
            <a class="navbar-brand" href="/">Vore Arts Fund</a>
        </h1>
        <nav id="navbar" class="navbar">
            <ul>
                <li>
                    <?= $this->Html->link('Home', '/', ['class' => 'nav-link']) ?>
                </li>
                <li>
                    <?= $this->Html->linkFromPath('Vote', 'Votes::index', [], ['class' => 'nav-link']) ?>
                </li>
                <?php if ($isLoggedIn): ?>
                    <?php if ($hasApplications): ?>
                        <li>
                            <?= $this->Html->linkFromPath('My Applications', 'Applications::index', [], ['class' => 'nav-link']) ?>
                        </li>
                    <?php endif; ?>
                    <li>
                        <?= $this->Html->linkFromPath('Account', 'Users::account', [], ['class' => 'nav-link']) ?>
                    </li>
                    <li>
                        <?= $this->Html->linkFromPath('Log Out', 'Users::logout', [], ['class' => 'nav-link']) ?>
                    </li>
                    <?php if ($isAdmin): ?>
                        <li>
                            <?= $this->Html->linkFromPath('Admin', 'Admin::index', [], ['class' => 'nav-link']) ?>
                        </li>
                    <?php endif; ?>
                    <?php if (!$isVerified): ?>
                        <li>
                            <?= $this->Html->linkFromPath('Verify', 'Users::verify', [], ['class' => 'nav-link']) ?>
                        </li>
                    <?php endif; ?>
                <?php else: ?>
                    <li>
                        <?= $this->Html->linkFromPath('Register', 'Users::register', [], ['class' => 'nav-link']) ?>
                    </li>
                    <li>
                        <?= $this->Html->linkFromPath('Login', 'Users::login', [], ['class' => 'nav-link']) ?>
                    </li>
                <?php endif; ?>
                <li>
                    <?= $this->Html->linkFromPath(
                        'Apply',
                        'Applications::apply',
                        [],
                        [
                            'class' => 'nav-link',
                            'id' => 'navbar-cta',
                        ]
                    ) ?>
                </li>
            </ul>
            <i class="fa-solid fa-bars mobile-nav-toggle"></i>
        </nav>
    </div>
</header>
