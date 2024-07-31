<?php

return [
    'ip' => env('MT5_SERVER_IP'),
    'port' => env('MT5_SERVER_PORT', 443),
    'login' => env('MT5_SERVER_WEB_LOGIN'),
    'password' => env('MT5_SERVER_WEB_PASSWORD'),
    'timeout' => env('MT5_SERVER_TIMEOUT', 30),
];
