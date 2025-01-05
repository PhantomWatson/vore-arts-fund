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
<?= $userName ?>,

you've received a message from the Vore Arts Fund review committee regarding your project "<?= $project->title ?>":

<?= nl2br($message) ?>

To reply to this message, please visit the Messages page for this project: <?= $replyUrl ?>
