<?php
$googleAnalyticsId = \Cake\Core\Configure::read('googleAnalyticsId');
?>
<?php if ($googleAnalyticsId): ?>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-JC9T3PEC8M"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', '<?= $googleAnalyticsId ?>');
    </script>
<?php endif; ?>

