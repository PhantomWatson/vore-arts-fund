<?php
/**
 * @var \App\View\AppView $this
 * @var bool $hasProjects
 * @var bool $isAdmin
 * @var bool $isLoggedIn
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
                        'About',
                        ['controller' => 'Pages', 'action' => 'about', 'prefix' => false],
                        ['class' => 'nav-link']
                    ) ?>
                </li>
                <li>
                    <?= $this->Html->link(
                        'Vote',
                        ['controller' => 'Votes', 'action' => 'index', 'prefix' => false],
                        ['class' => 'nav-link']
                    ) ?>
                </li>
                <li>
                    <?= $this->Html->link(
                        'Donate',
                        ['controller' => 'Donate', 'action' => 'index', 'prefix' => false],
                        ['class' => 'nav-link']
                    ) ?>
                </li>
                <?php if ($isLoggedIn): ?>
                    <li class="nav-item dropdown">
                        <?= $this->Html->link(
                            'Account',
                            ['controller' => 'Users', 'action' => 'account', 'prefix' => false],
                            ['class' => 'nav-link dropdown-toggle', 'data-bs-toggle' => 'dropdown']
                        ) ?>
                        <ul class="dropdown-menu">
                            <li>
                                <?= $this->Html->link(
                                    'Account',
                                    ['controller' => 'Users', 'action' => 'account', 'prefix' => false],
                                    ['class' => 'dropdown-item']
                                ) ?>
                            </li>
                            <?php if ($hasProjects): ?>
                                <li>
                                    <?= $this->Html->link(
                                        'My Projects',
                                        ['prefix' => 'My', 'controller' => 'Projects', 'action' => 'index'],
                                        ['class' => 'nav-link']
                                    ) ?>
                                </li>
                            <?php endif; ?>
                            <li>
                                <?= $this->Html->link(
                                    'Log out',
                                    ['controller' => 'Users', 'action' => 'logout', 'prefix' => false],
                                    ['class' => 'dropdown-item']
                                ) ?>
                            </li>
                        </ul>
                    </li>
                    <?php if ($isAdmin): ?>
                        <li class="nav-item dropdown">
                            <?= $this->Html->link(
                                'Admin',
                                ['controller' => 'Admin', 'action' => 'index', 'prefix' => 'Admin'],
                                ['class' => 'nav-link dropdown-toggle', 'data-bs-toggle' => 'dropdown']
                            ) ?>
                            <ul class="dropdown-menu">
                                <li>
                                    <?= $this->Html->link(
                                        'Projects',
                                        [
                                            'prefix' => 'Admin',
                                            'controller' => 'Projects',
                                            'action' => 'index',
                                        ],
                                        ['class' => 'dropdown-item']
                                    ) ?>
                                </li>
                                <li>
                                    <?= $this->Html->link(
                                        'Funding Cycles',
                                        [
                                            'prefix' => 'Admin',
                                            'controller' => 'FundingCycles',
                                            'action' => 'index',
                                        ],
                                        ['class' => 'dropdown-item']
                                    ) ?>
                                </li>
                                <li>
                                    <?= $this->Html->link(
                                        'Questions',
                                        [
                                            'prefix' => 'Admin',
                                            'controller' => 'Questions',
                                            'action' => 'index',
                                        ],
                                        ['class' => 'dropdown-item']
                                    ) ?>
                                </li>
                            </ul>
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
                        ['controller' => 'Projects', 'action' => 'apply', 'prefix' => false],
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
