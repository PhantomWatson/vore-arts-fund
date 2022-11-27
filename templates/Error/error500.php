<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\Database\StatementInterface $error
 * @var string $message
 * @var string $url
 */
use Cake\Core\Configure;
use Cake\Error\Debugger;

$this->layout = 'error';

if (Configure::read('debug')) :
    $this->layout = 'dev_error';

    $this->assign('title', $message);
    $this->assign('templateName', 'error500.php');

    $this->start('file');
?>
    <?php if (!empty($error->queryString)) : ?>
        <p class="notice">
            <strong>SQL Query: </strong>
            <?= h($error->queryString) ?>
        </p>
    <?php endif; ?>
    <?php if (!empty($error->params)) : ?>
        <strong>SQL Query Params: </strong>
        <?php Debugger::dump($error->params) ?>
    <?php endif; ?>
    <?php if ($error instanceof Error) : ?>
        <strong>Error in: </strong>
        <?= sprintf('%s, line %s', str_replace(ROOT, 'ROOT', $error->getFile()), $error->getLine()) ?>
    <?php endif; ?>

    <?= $this->element('auto_table_warning') ?>
    <?php $this->end(); ?>
<?php endif; ?>

<h2><?= __d('cake', 'An Internal Error Has Occurred') ?></h2>

<div class="alert alert-danger">
    <p>
        Sorry, but there was a problem loading that page. Please try again and <a href="/contact">contact us</a> if you need
        assistance.
    </p>
    <p>
        Details: <?= h($message) ?>
    </p>
</div>

<?= $this->Html->link(
    'Back',
    'javascript:history.back()',
    ['class' => 'btn btn-primary']
) ?>
