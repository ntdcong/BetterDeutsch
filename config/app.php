<?php
declare(strict_types=1);

return [
    'name' => 'BetterDeutsch',
    'env' => 'development', // 'production' or 'development'
    'debug' => true,
    'timezone' => 'Asia/Ho_Chi_Minh',
    'secure_session' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
];
