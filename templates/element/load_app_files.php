<?php
/**
 * @var \App\View\AppView $this
 * @var string[][] $toLoad
 * @var string $jsType
 */
foreach ($toLoad['js'] as $file) {
    $jsConfig = [];
    if (isset($jsType)) {
        $jsConfig['type'] = $jsType;
    }
    echo $this->Html->script($file, $jsConfig);
}
foreach ($toLoad['css'] as $file) {
    $this->Html->css($file, ['block' => true]);
}
