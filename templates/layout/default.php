<?php
/**
 * @var \App\View\AppView $this
 * @var array $breadcrumbs
 * @var string $currentBreadcrumb
 */

$title = $title ?? $this->fetch('title');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?= $this->element('analytics') ?>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        Vore Arts Fund
        <?= $title ? " - $title" : '' ?>
    </title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico?v=1705648439" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png" />
    <link rel="manifest" href="/site.webmanifest" />
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5" />
    <meta name="msapplication-TileColor" content="#00a300" />
    <meta name="theme-color" content="#E4E6C3" />

    <?= $this->Html->css('style.css') ?>
    <?= $this->Html->script('main') ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>

    <link rel="stylesheet" href="/fontawesome/css/fontawesome.min.css?t=1736535900" />
    <link rel="stylesheet" href="/fontawesome/css/brands.min.css?t=1736535900" />
    <link rel="stylesheet" href="/fontawesome/css/solid.min.css?t=1736535900" />
    <link rel="stylesheet" href="/fontawesome/css/regular.min.css?t=1736535900" />
    <link rel="stylesheet" href="/muncie-events-icon-font/css/icons.css" />

    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet" />
</head>
<body>
    <?= $this->element('navbar') ?>
    <main class="container clearfix">
        <?= $this->Flash->render() ?>
        <?= $this->element('breadcrumbs', compact('breadcrumbs', 'currentBreadcrumb', 'title')) ?>
        <?= $this->title() ?>
        <?= $this->fetch('content') ?>
        <?= $this->element('footer') ?>
    </main>
</body>
</html>
