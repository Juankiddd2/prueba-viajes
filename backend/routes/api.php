<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// Rutas para ciudades y consultas
Route::get('/ciudades', [\App\Http\Controllers\CiudadController::class, 'index']);
Route::post('/consulta', [\App\Http\Controllers\ConsultaController::class, 'store']);
