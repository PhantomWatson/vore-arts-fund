<?php
/**
 * @var \App\View\AppView $this
 * @var array $breadcrumbs
 * @var string $currentBreadcrumb
 * @var string $title
 */
if (!$currentBreadcrumb) {
    $currentBreadcrumb = $title;
}
$target = $this->request->getRequestTarget();
$here = explode('?', $target)[0];
if ($here == '/') {
    return;
}

// Avoid redundant "Home > Controller / Group Title > Page Title" breadcrumbs, like "Home > Admin > Admin"
if ($breadcrumbs && end($breadcrumbs)[0] == $currentBreadcrumb) {
    array_pop($breadcrumbs);
}
?>

<?php if (count($breadcrumbs) > 1): ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <?php foreach ($breadcrumbs as $breadcrumb): ?>
                <li class="breadcrumb-item">
                    <?php if ($breadcrumb[1]): ?>
                        <?= $this->Html->link($breadcrumb[0], $breadcrumb[1]) ?>
                    <?php else: ?>
                        <?= $breadcrumb[0] ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
            <li class="breadcrumb-item active" aria-current="page">
                <?= $currentBreadcrumb ?>
            </li>
        </ol>
    </nav>
<?php endif; ?>
