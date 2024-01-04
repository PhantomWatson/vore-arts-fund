<?php
/**
 * @var \App\Model\Entity\Project $project
 * @var string $message
 * @var \App\View\AppView $this
 * @var string $userName
 */
?>
<?= $userName ?>, you've received a message from the Vore Arts Fund review committee regarding your project "<?= $project->title ?>":

<?= nl2br($message) ?>
