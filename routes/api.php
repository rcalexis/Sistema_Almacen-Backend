<?php

use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\AuthController;

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

    Route::prefix('productos')->group(function () {
        Route::get('/', [ProductoController::class, 'listarProductos']);
        Route::get('/{id}', [ProductoController::class, 'verProducto']);
        Route::post('/', [ProductoController::class, 'crearProducto']);
        Route::post('/{id}/aumentar', [ProductoController::class, 'aumentarInventario']);
        Route::post('/{id}/sacar', [ProductoController::class, 'sacarInventario']);
        Route::patch('/{id}/baja', [ProductoController::class, 'darBajaProducto']);
        Route::patch('/{id}/reactivar', [ProductoController::class, 'reactivarProducto']);
    });

});