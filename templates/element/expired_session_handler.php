<?php
/**
 * Sets up a form on this page to be protected from losing data if submitting with an expired PHP session
 *
 * Requires $formId being provided
 *
 * @var \App\View\AppView $this
 * @var string $formId
 */

$this->Html->script('/js/expired-session-handler.js', ['block' => true]);
?>

<?= $this->element('Users/login_modal', compact('formId')) ?>
<script>
    new ExpiredSessionHandler(<?= json_encode($formId) ?>);
</script>
