<?php

return [
    'Authentication' => [],
    'Bake' => [
        'onlyCli' => true,
        'optional' => true,
    ],
    'DebugKit' => [
        'onlyDebug' => true,
    ],
    'IdeHelper' => [
        'onlyDebug' => true,
        'optional' => true,
    ],
    'Migrations' => [
        'onlyCli' => true,
    ],
    'Queue' => [
        'routes' => false,
    ],
];
