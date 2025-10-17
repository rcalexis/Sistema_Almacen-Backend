<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PDOException;

class ProductoController extends Controller
{
    public function crearProducto(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:100',
                'descripcion' => 'nullable|string|max:255',
            ]);

            $usuario = $request->user();

            $idNuevo = DB::select('SELECT fn_crear_producto(?, ?, ?) AS id', [
                $request->nombre,
                $request->descripcion,
                $usuario->id_usuario
            ]);

            if (empty($idNuevo)) {
                return response()->json([
                    'mensaje' => 'Error al crear el producto'
                ], 500);
            }

            return response()->json([
                'mensaje' => 'Producto creado correctamente',
                'id_producto' => $idNuevo[0]->id
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'mensaje' => 'Error en la validación',
                'errores' => $e->errors()
            ], 422);
        } catch (PDOException $e) {
            $mensaje = $e->getMessage();
            if (str_contains($mensaje, 'Solo los administradores')) {
                return response()->json([
                    'mensaje' => 'No tienes permisos para crear productos'
                ], 403);
            }
            return response()->json([
                'mensaje' => 'Error en la base de datos',
                'error' => $mensaje
            ], 500);
        }
    }

    public function listarProductos(Request $request)
    {
        try {
            $estatus = $request->query('estatus');
            
            $productos = DB::select('SELECT * FROM fn_listar_productos(?)', [
                $estatus !== null ? filter_var($estatus, FILTER_VALIDATE_BOOLEAN) : null
            ]);
            
            return response()->json($productos);
        } catch (PDOException $e) {
            return response()->json([
                'mensaje' => 'Error al obtener los productos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function verProducto($id)
    {
        try {
            $producto = DB::select('SELECT * FROM fn_ver_producto(?)', [$id]);

            if (empty($producto)) {
                return response()->json(['mensaje' => 'Producto no encontrado'], 404);
            }

            return response()->json($producto[0]);
        } catch (PDOException $e) {
            return response()->json([
                'mensaje' => 'Error al obtener el producto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function aumentarInventario(Request $request, $id)
    {
        try {
            $request->validate([
                'cantidad' => 'required|integer|min:1'
            ]);

            $usuario = $request->user();

            DB::statement('SELECT fn_aumentar_inventario(?, ?, ?)', [
                $id,
                $request->cantidad,
                $usuario->id_usuario
            ]);

            return response()->json([
                'mensaje' => 'Inventario aumentado correctamente'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'mensaje' => 'Error en la validacion',
                'errores' => $e->errors()
            ], 422);
        } catch (PDOException $e) {
            $mensaje = $e->getMessage();
            if (str_contains($mensaje, 'Solo los administradores')) {
                return response()->json([
                    'mensaje' => 'No tienes permisos para aumentar inventario'
                ], 403);
            } elseif (str_contains($mensaje, 'No se puede aumentar')) {
                return response()->json([
                    'mensaje' => 'No se puede realizar la operacion',
                    'error' => $mensaje
                ], 400);
            } elseif (str_contains($mensaje, 'La cantidad debe ser')) {
                return response()->json([
                    'mensaje' => 'Cantidad invalida',
                    'error' => $mensaje
                ], 400);
            }
            return response()->json([
                'mensaje' => 'Error en la base de datos',
                'error' => $mensaje
            ], 500);
        }
    }

    public function sacarInventario(Request $request, $id)
    {
        try {
            $request->validate([
                'cantidad' => 'required|integer|min:1'
            ]);

            $usuario = $request->user();

            DB::statement('SELECT fn_sacar_inventario(?, ?, ?)', [
                $id,
                $request->cantidad,
                $usuario->id_usuario
            ]);

            return response()->json([
                'mensaje' => 'Inventario reducido correctamente'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'mensaje' => 'Error en la validacion',
                'errores' => $e->errors()
            ], 422);
        } catch (PDOException $e) {
            $mensaje = $e->getMessage();
            if (str_contains($mensaje, 'Solo los almacenistas')) {
                return response()->json([
                    'mensaje' => 'No tienes permisos para sacar productos'
                ], 403);
            } elseif (str_contains($mensaje, 'No hay suficientes productos')) {
                return response()->json([
                    'mensaje' => 'Inventario no insuficiente',
                    'error' => $mensaje
                ], 400);
            } elseif (str_contains($mensaje, 'No se puede sacar')) {
                return response()->json([
                    'mensaje' => 'No se puede realizar la operacion',
                    'error' => $mensaje
                ], 400);
            } elseif (str_contains($mensaje, 'La cantidad debe ser')) {
                return response()->json([
                    'mensaje' => 'Cantidad invalida',
                    'error' => $mensaje
                ], 400);
            }
            return response()->json([
                'mensaje' => 'Error en la base de datos',
                'error' => $mensaje
            ], 500);
        }
    }
    
    
}
