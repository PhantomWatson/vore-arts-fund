<?php
/**
 * @var \App\Model\Entity\Project $project
 * @var string $message
 * @var \App\View\AppView $this
 * @var string $userName
 * @var string $replyUrl
 */

?>
<p>
    <?= $userName ?>,
</p>

<p>
    You've received a message from the Vore Arts Fund review committee regarding your project
    <strong><?= $project->title ?></strong>:
</p>

<blockquote>
    <?= $message ?>
</blockquote>

<p>
    To reply to this message, please visit <a href="<?= $replyUrl ?>">the Messages page for this project</a>.
</p>
