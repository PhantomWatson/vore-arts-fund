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
                    <?= $this->Html->link(
                        'Vote',
                        ['controller' => 'Votes', 'action' => 'index', 'prefix' => false],
                        ['class' => 'nav-link']
                    ) ?>
                </li>
                <?php if ($isLoggedIn): ?>
                    <?php if ($hasApplications): ?>
                        <li>
                            <?= $this->Html->link(
                                'My Applications',
                                ['controller' => 'Applications', 'action' => 'index', 'prefix' => false],
                                ['class' => 'nav-link']
                            ) ?>
                        </li>
                    <?php endif; ?>
                    <li>
                        <?= $this->Html->link(
                            'Account',
                            ['controller' => 'Users', 'action' => 'account', 'prefix' => false],
                            ['class' => 'nav-link']
                        ) ?>
                    </li>
                    <li>
                        <?= $this->Html->link(
                            'Log Out',
                            ['controller' => 'Users', 'action' => 'logout', 'prefix' => false],
                            ['class' => 'nav-link']
                        ) ?>
                    </li>
                    <?php if ($isAdmin): ?>
                        <li>
                            <?= $this->Html->link(
                                'Admin',
                                ['controller' => 'Admin', 'action' => 'index', 'prefix' => 'Admin'],
                                ['class' => 'nav-link']
                            ) ?>
                        </li>
                    <?php endif; ?>
                    <?php if (!$isVerified): ?>
                        <li>
                            <?= $this->Html->link(
                                'Verify',
                                ['controller' => 'Users', 'action' => 'verify', 'prefix' => false],
                                ['class' => 'nav-link']
                            ) ?>
                        </li>
                    <?php endif; ?>
                <?php else: ?>
                    <li>
                        <?= $this->Html->link(
                            'Register',
                            ['controller' => 'Users', 'action' => 'register', 'prefix' => false],
                            ['class' => 'nav-link']
                        ) ?>
                    </li>
                    <li>
                        <?= $this->Html->link(
                            'Login',
                            ['controller' => 'Users', 'action' => 'login', 'prefix' => false],
                            ['class' => 'nav-link']
                        ) ?>
                    </li>
                <?php endif; ?>
                <li>
                    <?= $this->Html->link(
                        'Apply',
                        ['controller' => 'Applications', 'action' => 'apply', 'prefix' => false],
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
