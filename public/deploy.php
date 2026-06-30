<?php
if (($_GET['token'] ?? '') !== 'habitat2026deploy') {
    http_response_code(403);
    die('403 Forbidden');
}

$appPath = __DIR__ . '/../instituto_habitat';

require $appPath . '/vendor/autoload.php';

$app = require $appPath . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$salida = [];

try {
    Artisan::call('migrate', ['--force' => true]);
    $salida[] = '✅ migrate: ' . trim(Artisan::output());
} catch (Throwable $e) {
    $salida[] = '❌ migrate ERROR: ' . $e->getMessage();
}

try {
    Artisan::call('db:seed', ['--force' => true]);
    $salida[] = '✅ seed: ' . trim(Artisan::output());
} catch (Throwable $e) {
    $salida[] = '❌ seed ERROR: ' . $e->getMessage();
}

try {
    Artisan::call('optimize');
    $salida[] = '✅ optimize: OK';
} catch (Throwable $e) {
    $salida[] = '❌ optimize ERROR: ' . $e->getMessage();
}

try {
    Artisan::call('storage:link');
    $salida[] = '✅ storage:link: OK';
} catch (Throwable $e) {
    $salida[] = '❌ storage:link ERROR: ' . $e->getMessage();
}

echo '<pre style="font-family:monospace;font-size:14px;padding:24px">';
echo implode("\n", $salida);
echo '</pre>';
