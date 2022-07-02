<?php
/**
 * @var \App\View\AppView $this
 * @var array $params
 * @var string $message
 */
echo $this->element(
    'flash/base',
    [
        'alertClass' => 'info',
        'message' => $message,
        'params' => $params,
    ]
);
