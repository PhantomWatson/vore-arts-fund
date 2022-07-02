<?php
/**
 * @var \App\View\AppView $this
 * @var array $params
 * @var string $message
 */
echo $this->element(
    'flash/base',
    [
        'alertClass' => 'warning',
        'message' => $message,
        'params' => $params,
    ]
);
