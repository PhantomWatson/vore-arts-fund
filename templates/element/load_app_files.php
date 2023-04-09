<?php
/**
 * @var \App\View\AppView $this
 * @var string[][] $toLoad
 * @var string $dir
 */
foreach ($toLoad['js'] as $file) {
    echo $this->Html->script("/$dir/dist/$file");
}
foreach ($toLoad['css'] as $file) {
    $this->Html->css("/$dir/dist/styles/$file", ['block' => true]);
}
