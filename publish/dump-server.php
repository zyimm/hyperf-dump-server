<?php

use function Hyperf\Support\env;

return [

    // 监听地址
    'host' => env('DUMP_SERVER_HOST', 'tcp://0.0.0.0:7997'),
];
