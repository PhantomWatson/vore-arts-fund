<?php
/**
 * @var \App\Model\Entity\Project $project
 * @var string $message
 * @var \App\View\AppView $this
 * @var string $userName
 */
?>
<p>
    <?= $userName ?>, you've received a message from the Vore Arts Fund review committee regarding your project
    <strong><?= $project->title ?></strong>:
</p>

<blockquote>
    <?= $message ?>
</blockquote>
