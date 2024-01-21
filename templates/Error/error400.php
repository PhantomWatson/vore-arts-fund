<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\Database\StatementInterface|HttpException $error
 * @var string $message
 * @var string $url
 */
use Cake\Core\Configure;
use Cake\Error\Debugger;
use Cake\Http\Exception\HttpException;

$this->layout = 'error';

if (Configure::read('debug')) :
    $this->layout = 'dev_error';

    $this->assign('title', $message);
    $this->assign('templateName', 'error400.php');

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
    <?= $this->element('auto_table_warning') ?>
    <?php $this->end(); ?>
<?php endif; ?>

<h2><?= h($message) ?></h2>

<p class="alert alert-danger">
    <?php if ($error->getCode() == 401): ?>
        Please
        <?= $this->Html->link(
            'log in',
            \App\Application::LOGIN_URL,
        ) ?>
        to continue.
    <?php elseif ($error->getCode() == 403): ?>
        Sorry, but you don't have access to that page. You might be logged into the wrong account.
        Please <a href="/contact">contact us</a> if you need assistance.
    <?php elseif ($error->getCode() == 404): ?>
        Sorry, but that page wasn't found. Please <a href="/contact">contact us</a> if you need assistance.
    <?php else: ?>
        There was an error loading that page. Please <a href="/contact">contact us</a> if you need assistance.
    <?php endif; ?>
</p>

<?= $this->Html->link(
    'Back',
    'javascript:history.back()',
    ['class' => 'btn btn-primary']
) ?>
