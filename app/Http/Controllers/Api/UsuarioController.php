<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function crearUsuario(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'correo' => 'required|email|unique:usuarios,correo',
            'contrasena' => 'required|string|min:6',
            'id_rol' => 'required|integer'
        ]);

        $hashedPassword = Hash::make($request->contrasena);

        $idNuevo = DB::select('SELECT fn_crear_usuario(?, ?, ?, ?) AS id', [
            $request->nombre,
            $request->correo,
            $hashedPassword,
            $request->id_rol
        ]);

        return response()->json([
            'mensaje' => 'Usuario creado correctamente',
            'id_usuario' => $idNuevo[0]->id
        ], 201);
    }
}
