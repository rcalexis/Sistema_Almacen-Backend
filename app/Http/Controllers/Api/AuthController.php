<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;



class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'correo' => 'required|email',
                'contrasena' => 'required|string',
            ]);
            $resultado = DB::select('SELECT * FROM fn_buscar_usuario_por_correo(?)', [
                $request->correo
            ]);
            
            if (empty($resultado)) {
                return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
            }

            $usuario = $resultado[0];

            if ($usuario->estatus === false) {
                return response()->json(['mensaje' => 'El usuario est dado de baja'], 403);
            }

            if (!Hash::check($request->contrasena, $usuario->contrasena)) {
                return response()->json(['mensaje' => 'Contraseña incorrecta'], 401);
            }

            $usuarioEloquent = Usuario::find($usuario->id_usuario);

            $usuarioEloquent->tokens()->delete();

            $token = $usuarioEloquent->createToken('auth_token')->plainTextToken;

            return response()->json([
                'mensaje' => 'Login exitoso',
                'token' => $token,
                'usuario' => [
                    'id_usuario' => $usuario->id_usuario,
                    'nombre' => $usuario->nombre,
                    'correo' => $usuario->correo,
                    'id_rol' => $usuario->id_rol,
                ],
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'mensaje' => 'Error en la validacion',
                'errores' => $e->errors()
            ], 422);
        } catch (PDOException $e) {
            return response()->json([
                'mensaje' => 'Error en la base de datos',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error inesperado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['mensaje' => 'No hay usuario autenticado'], 401);
        }

        $user->currentAccessToken()->delete();

        return response()->json(['mensaje' => 'Sesion cerrada correctamente']);
    }
}
