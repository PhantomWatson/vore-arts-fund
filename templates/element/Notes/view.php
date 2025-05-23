<?php
/**
 * @var \App\Model\Entity\Note $note
 * @var \App\View\AppView $this
 */

use App\Model\Entity\Note;

$isAdmin = $this->getRequest()->getParam('prefix') == 'Admin';
?>

<article class="note">
    <header class="note-header row">
        <p class="note-type col-6">
            <?= Note::getNoteTypeIcon($note->type) ?>
            <?= Note::getNoteTypePublicName($note->type, $isAdmin) ?>
        </p>
        <p class="note-date col-6">
            <?php if ($isAdmin): ?>
                <?= $note->user->name ?> -
            <?php endif; ?>
            <?= $note->created->setTimezone(\App\Application::LOCAL_TIMEZONE)->format('F j, Y') ?>
        </p>
    </header>
    <p>
        <?= nl2br($note->body) ?>
    </p>
</article>
