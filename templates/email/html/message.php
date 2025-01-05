<?php
/**
 * @var \App\Model\Entity\Project $project
 * @var string $message
 * @var \App\View\AppView $this
 * @var string $userName
 */

use Cake\Routing\Router;

$replyUrl = Router::url([
    'prefix' => 'My',
    'controller' => 'Projects',
    'action' => 'messages',
    'id' => $project->id
]);

?>
<p>
    <?= $userName ?>,
</p>

<p>
    you've received a message from the Vore Arts Fund review committee regarding your project
    <strong><?= $project->title ?></strong>:
</p>

<blockquote>
    <?= $message ?>
</blockquote>

<p>
    To reply to this message, please visit <a href="<?= $replyUrl ?>">the Messages page for this project</a>.
</p>
