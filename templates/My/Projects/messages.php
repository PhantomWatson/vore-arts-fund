<?php
/**
 * @var \Cake\ORM\ResultSet|\App\Model\Entity\Note[] $notes
 */
?>

<?php if ($notes->count()): ?>
    <?php foreach ($notes as $note): ?>
        <?= $this->element('Notes/view', compact('note')) ?>
    <?php endforeach; ?>
<?php else: ?>
    <p>
        You have received no messages about this project so far.
    </p>
<?php endif; ?>

<p>
    If you'd like to contact the Vore Arts Fund staff about this project, please visit
    <?= $this->Html->link('our contact page', ['prefix' => false, 'controller' => 'Pages', 'action' => 'contact']) ?>.
</p>
