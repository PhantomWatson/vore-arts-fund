<?php
/**
 * @var \App\View\AppView $this
 * @var bool $hasProjects
 * @var bool $isAdmin
 * @var bool $isLoggedIn
 */
$links = [
    [
        'Home',
        '/',
        ['class' => 'nav-link']
    ],
    [
        'Projects',
        ['controller' => 'Projects', 'action' => 'index', 'prefix' => false],
        ['class' => 'nav-link']
    ],
    [
        'About',
        ['controller' => 'Pages', 'action' => 'about', 'prefix' => false],
        ['class' => 'nav-link']
    ],
    [
        'Vote',
        ['controller' => 'Votes', 'action' => 'index', 'prefix' => false],
        ['class' => 'nav-link']
    ],
    [
        'Donate',
        ['controller' => 'Donate', 'action' => 'index', 'prefix' => false],
        ['class' => 'nav-link']
    ]
];

$loggedInLinks = [
    [
        'Account',
        ['controller' => 'Users', 'action' => 'account', 'prefix' => false],
        ['class' => 'dropdown-item']
    ]
];
if ($hasProjects) {
    $loggedInLinks[] = [
        'My Projects',
        ['prefix' => 'My', 'controller' => 'Projects', 'action' => 'index'],
        ['class' => 'dropdown-item']
    ];
}
$loggedInLinks[] = [
    'Log out',
    ['controller' => 'Users', 'action' => 'logout', 'prefix' => false],
    ['class' => 'dropdown-item']
];

$adminLinks = [
    [
        'Funding Cycles',
        [
            'prefix' => 'Admin',
            'controller' => 'FundingCycles',
            'action' => 'index',
        ],
        ['class' => 'dropdown-item']
    ],
    [
        'Projects',
        [
            'prefix' => 'Admin',
            'controller' => 'Projects',
            'action' => 'index',
        ],
        ['class' => 'dropdown-item']
    ],
    [
        'Questions',
        [
            'prefix' => 'Admin',
            'controller' => 'Questions',
            'action' => 'index',
        ],
        ['class' => 'dropdown-item']
    ],
    [
        'Transactions',
        [
            'prefix' => 'Admin',
            'controller' => 'Transactions',
            'action' => 'index',
        ],
        ['class' => 'dropdown-item']
    ],
    [
        'Votes',
        [
            'prefix' => 'Admin',
            'controller' => 'Votes',
            'action' => 'index',
        ],
        ['class' => 'dropdown-item']
    ]
];

function echoLinks(\App\View\AppView $appView, $links, $navbarItemClass = 'nav-item') {
    foreach ($links as $link) {
        echo '<li class="' . $navbarItemClass . '">' . $appView->Html->link(...$link) . '</li>';
    }
}

?>
<header id="header" class="fixed-top">
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <h1 class="logo">
                <a class="navbar-brand" href="/">
                    <img src="/img/logo-wordmark.png" height="70" alt="Vore Arts Fund" title="Vore Arts Fund" />
                </a>
            </h1>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php echoLinks($this, $links); ?>
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item dropdown">
                            <button class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Account</button>
                            <ul class="dropdown-menu">
                                <?php echoLinks($this, $loggedInLinks, ''); ?>
                            </ul>
                        </li>
                        <?php if ($isAdmin): ?>
                            <li class="nav-item dropdown">
                                <button class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Admin</button>
                                <ul class="dropdown-menu">
                                    <?php echoLinks($this, $adminLinks, ''); ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li>
                            <?= $this->Html->link(
                                'Login / Register',
                                \App\Application::LOGIN_URL,
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
            </div>
        </div>
    </nav>
</header>
