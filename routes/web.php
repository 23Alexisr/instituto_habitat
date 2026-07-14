<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/certificados/{certificado}/descargar', [\App\Http\Controllers\CertificadoController::class, 'descargar'])
    ->middleware(['auth'])
    ->name('certificados.descargar');

Route::get('/verificar/{codigo}', [\App\Http\Controllers\VerificadorController::class, 'verificar'])
    ->middleware('throttle:15,1')
    ->name('certificados.verificar');


