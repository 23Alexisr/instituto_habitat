<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/certificados/{certificado}/descargar', [\App\Http\Controllers\CertificadoController::class, 'descargar'])
    ->middleware(['auth'])
    ->name('certificados.descargar');

Route::get('/verificar/{codigo}', [\App\Http\Controllers\VerificadorController::class, 'verificar'])
    ->name('certificados.verificar');

// RUTA TEMPORAL — solo para deploy inicial, ELIMINAR después de correr las migraciones
Route::get('/deploy-migrate/{token}', function (string $token) {
    if ($token !== config('app.key')) {
        abort(403);
    }
    try {
        \Artisan::call('migrate', ['--force' => true]);
        return '<pre>OK: ' . \Artisan::output() . '</pre>';
    } catch (\Throwable $e) {
        return '<pre>ERROR: ' . $e->getMessage() . '</pre>';
    }
});
