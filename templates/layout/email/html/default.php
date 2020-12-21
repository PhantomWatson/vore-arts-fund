<?php
/**
 * @var \App\View\AppView $this
 * @var string|null $subject
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html lang="en">
<head>
    <title><?= $subject ?? '' ?></title>
</head>
<body>
    <?= $this->fetch('content') ?>
</body>
</html>
