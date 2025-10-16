<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use PDOException;

class UsuarioController extends Controller
{
    public function crearUsuario(Request $request)
    {
        try {
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

            if (empty($idNuevo)) {
                return response()->json([
                    'mensaje' => 'Error al crear el usuario'
                ], 500);
            }

            return response()->json([
                'mensaje' => 'Usuario creado correctamente',
                'id_usuario' => $idNuevo[0]->id
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'mensaje' => 'Error en la validacion',
                'errores' => $e->errors()
            ], 422);

        } catch (PDOException $e) {
            return response()->json([
                'mensaje' => 'Error en la base de datos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function listarUsuarios()
    {
        try {
            $usuarios = DB::select('SELECT * FROM fn_listar_usuarios()');
            return response()->json($usuarios);
        } catch (PDOException $e) {
            return response()->json([
                'mensaje' => 'Error al obtener los usuarios',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function verUsuario($id)
    {
        try {
            $usuario = DB::select('SELECT * FROM fn_ver_usuario(?)', [$id]);

            if (empty($usuario)) {
                return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
            }

            return response()->json($usuario[0]);
        } catch (PDOException $e) {
            return response()->json([
                'mensaje' => 'Error al obtener el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function actualizarUsuario(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'nullable|string|max:100',
            'correo' => 'nullable|email|unique:usuarios,correo,' . $id . ',id_usuario',
            'contrasena' => 'nullable|string|min:6',
            'id_rol' => 'nullable|integer',
            'estatus' => 'nullable|boolean'
        ]);

        try {
            $hashedPassword = $request->filled('contrasena') ? Hash::make($request->contrasena) : null;

            $usuarioExistente = DB::select('SELECT * FROM fn_ver_usuario(?)', [$id]);
            
            if (empty($usuarioExistente)) {
                return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
            }

            DB::statement('SELECT fn_actualizar_usuario(?, ?, ?, ?, ?, ?)', [
                $id,
                $request->nombre,
                $request->correo,
                $hashedPassword,
                $request->id_rol,
                $request->estatus
            ]);

            return response()->json(['mensaje' => 'Usuario actualizado correctamente']);

        } catch (PDOException $e) {
            return response()->json([
                'mensaje' => 'Error al actualizar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function eliminarUsuario($id)
    {
        try {
            $usuarioExistente = DB::select('SELECT * FROM fn_ver_usuario(?)', [$id]);
            
            if (empty($usuarioExistente)) {
                return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
            }

            if ($usuarioExistente[0]->estatus === false) {
                return response()->json(['mensaje' => 'El usuario ya se dio de baja'], 409);
            }

            DB::statement('SELECT fn_eliminar_usuario(?)', [$id]);
            return response()->json(['mensaje' => 'Usuario dado de baja correctamente']);

        } catch (PDOException $e) {
            return response()->json([
                'mensaje' => 'Error al eliminar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function reactivarUsuario($id)
    {
        try {
          
            $usuarioExistente = DB::select('SELECT * FROM fn_ver_usuario(?)', [$id]);
            
            if (empty($usuarioExistente)) {
                return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
            }

        
            if ($usuarioExistente[0]->estatus === true) {
                return response()->json(['mensaje' => 'El usuario ya esta activo'], 409);
            }

            DB::statement('SELECT fn_reactivar_usuario(?)', [$id]);
            return response()->json(['mensaje' => 'Usuario reactivado correctamente']);

        } catch (PDOException $e) {
            return response()->json([
                'mensaje' => 'Error al reactivar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
