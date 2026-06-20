<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/certificados/{certificado}/descargar', [\App\Http\Controllers\CertificadoController::class, 'descargar'])
    ->middleware(['auth'])
    ->name('certificados.descargar');
