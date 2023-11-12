<?php
/**
 * @var \App\View\AppView $this
 */
?>
<?php if ($this->Paginator->hasPrev() || $this->Paginator->hasNext()): ?>
    <div class="pagination">
        <ul class="pagination">
            <?= $this->Paginator->prev() ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next() ?>
        </ul>
    </div>
<?php endif; ?>
