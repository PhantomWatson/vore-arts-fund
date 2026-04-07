<?php
/**
 * @var string $alertClass
 * @var string $message
 * @var array $params
 * @var \App\View\AppView $this
 */
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<div class="alert alert-<?= $alertClass ?>" onclick="this.classList.add('hidden');">
    <div class="container">
        <?= $message ?>
    </div>
</div>
