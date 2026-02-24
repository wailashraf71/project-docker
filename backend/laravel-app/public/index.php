<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Suppress PHP 8.4+ tempnam() "system's temporary directory" notice from dependencies
set_error_handler(function (int $severity, string $message): bool {
    if (($severity === E_NOTICE || $severity === E_USER_NOTICE)
        && str_contains($message, 'tempnam(): file created in the system\'s temporary directory')) {
        return true;
    }
    return false;
}, E_NOTICE | E_USER_NOTICE);

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
