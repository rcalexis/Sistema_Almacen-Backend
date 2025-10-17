<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PDOException;

class MovimientoController extends Controller
{
    public function listarMovimientos(Request $request)
    {
        try {
            $tipo = $request->query('tipo_movimiento');
            $usuarioId = $request->user()->id_usuario;
            
            $movimientos = DB::select('SELECT * FROM fn_listar_movimientos(?, ?)', [
                $usuarioId,
                in_array($tipo, ['entrada', 'salida']) ? $tipo : null
            ]);

            return response()->json($movimientos);
        } catch (PDOException $e) {
            return response()->json([
                'mensaje' => 'Error al obtener los movimientos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
