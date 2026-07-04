<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
// En Hostinger, public_html/ y instituto_habitat/ son carpetas hermanas, por eso
// esta ruta apunta al vecino. En local (XAMPP, artisan serve) no existe esa carpeta
// hermana porque el repo completo ya ES instituto_habitat, así que caemos al padre.
$appPath = __DIR__ . '/../instituto_habitat';

if (! file_exists($appPath . '/vendor/autoload.php')) {
    $appPath = __DIR__ . '/..';
}

if (file_exists($maintenance = $appPath . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $appPath . '/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $appPath . '/bootstrap/app.php';

$app->handleRequest(Request::capture());
