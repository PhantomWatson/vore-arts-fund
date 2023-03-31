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
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        Vore Arts Fund
        <?= $title ? " - $title" : '' ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->Html->css('style.css') ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
    <link rel="stylesheet" href="/fontawesome/css/fontawesome.min.css" />
    <link rel="stylesheet" href="/fontawesome/css/solid.min.css" />

    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
            integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
    </script>
    <script src="/js/bootstrap.min.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
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
    <script src="/js/main.js"></script>
</body>
</html>
