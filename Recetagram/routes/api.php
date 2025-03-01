<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    // Rutas públicas
    Route::post('/register', [AuthController::class, 'store']);
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    // Rutas protegidas
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        // Rutas CRUD de usuarios
        Route::get('/users', [AuthController::class, 'index']);
        Route::get('/users/{user}', [AuthController::class, 'show']);
        Route::post('/users', [AuthController::class, 'store']);
        Route::put('/users/{user}', [AuthController::class, 'update']);
        Route::delete('/users/{user}', [AuthController::class, 'destroy']);
        
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
    });
});
