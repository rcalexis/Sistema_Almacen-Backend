<?php

use App\Http\Controllers\Api\UsuarioController;



Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {


    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('usuarios')->group(function () {
        Route::get('/', [UsuarioController::class, 'listarUsuarios']);
        Route::get('/{id}', [UsuarioController::class, 'verUsuario']);
        Route::post('/', [UsuarioController::class, 'crearUsuario']);
        Route::put('/{id}', [UsuarioController::class, 'actualizarUsuario']);
        Route::delete('/{id}', [UsuarioController::class, 'eliminarUsuario']);
        Route::patch('/{id}/reactivar', [UsuarioController::class, 'reactivarUsuario']);
    });

});