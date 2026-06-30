<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/certificados/{certificado}/descargar', [\App\Http\Controllers\CertificadoController::class, 'descargar'])
    ->middleware(['auth'])
    ->name('certificados.descargar');

Route::get('/verificar/{codigo}', [\App\Http\Controllers\VerificadorController::class, 'verificar'])
    ->name('certificados.verificar');

// RUTA TEMPORAL — solo para deploy inicial, ELIMINAR después de usarla
Route::get('/deploy/{token}', function (string $token) {
    if ($token !== 'habitat2026deploy') {
        abort(403);
    }
    $salida = [];
    try {
        \Artisan::call('migrate', ['--force' => true]);
        $salida[] = '✅ migrate: ' . trim(\Artisan::output());

        \Artisan::call('db:seed', ['--force' => true]);
        $salida[] = '✅ seed: ' . trim(\Artisan::output());

        \Artisan::call('optimize');
        $salida[] = '✅ optimize: OK';

        \Artisan::call('storage:link');
        $salida[] = '✅ storage:link: OK';
    } catch (\Throwable $e) {
        $salida[] = '❌ ERROR: ' . $e->getMessage();
    }
    return '<pre style="font-family:monospace;padding:20px">' . implode("\n", $salida) . '</pre>';
})->withoutMiddleware([
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\Session\Middleware\AuthenticateSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
]);
