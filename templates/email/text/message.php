<?php
/**
 * @var \App\Model\Entity\Project $project
 * @var string $message
 * @var \App\View\AppView $this
 * @var string $userName
 * @var string $replyUrl
 */
?>
<?= $userName ?>,

You've received a message from the Vore Arts Fund review committee regarding your project "<?= $project->title ?>":

<?= nl2br($message) ?>

To reply to this message, please visit the Messages page for this project: <?= $replyUrl ?>
