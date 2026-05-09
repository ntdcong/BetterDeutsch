<?php
declare(strict_types=1);

// Define Base Path
define('BASE_PATH', dirname(__DIR__));

// Load application configuration
$appConfig = require BASE_PATH . '/config/app.php';

// Set error reporting
if ($appConfig['debug'] ?? false) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// Set timezone
date_default_timezone_set($appConfig['timezone'] ?? 'UTC');

// Start secure session
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => $appConfig['secure_session'] ?? false,
        'use_strict_mode' => true,
    ]);
}

// Require Composer Autoloader
if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
    require BASE_PATH . '/vendor/autoload.php';
} else {
    die("Xin vui lòng chạy 'composer install' trước khi tiếp tục.");
}

// echo "<h1>Welcome to BetterDeutsch MVC!</h1>";

$router = new \App\Core\Router();

require BASE_PATH . '/src/routes.php';

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
