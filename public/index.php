<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Suppress broken pipe notices in development server (harmless errors)
// This happens when client disconnects before server finishes writing
// Note: This error occurs in vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php
// before this file is loaded, so we can't catch it here. It's suppressed via start-server.sh

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
