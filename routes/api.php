<?php

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login']);


Route::prefix('usuarios')->group(function () {
    Route::get('/', [UsuarioController::class, 'listarUsuarios']);
    Route::get('/{id}', [UsuarioController::class, 'verUsuario']);
    Route::post('/', [UsuarioController::class, 'crearUsuario']);
    Route::put('/{id}', [UsuarioController::class, 'actualizarUsuario']);
    Route::delete('/{id}', [UsuarioController::class, 'eliminarUsuario']);
    Route::patch('/{id}/reactivar', [UsuarioController::class, 'reactivarUsuario']);
});