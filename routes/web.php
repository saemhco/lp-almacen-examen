<?php

use App\Http\Controllers\FlagController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('inicio');
});

Route::get('/flags/admin', [FlagController::class, 'admin']);
Route::get('/flags/supervisor', [FlagController::class, 'supervisor']);

Route::get('/items', function () {
    return 'Hola soy la ruta de items WEB';
});
