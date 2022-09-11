<?php
/** @var string $title */
?>
<?php if ($title ?? false): ?>
    <div class="pb-2 mb-2 border-bottom">
        <h1>
            <?= $title ?>
        </h1>
    </div>
<?php endif; ?>
