<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Objeto;
use App\Models\Deteccion;
use Illuminate\Support\Facades\DB;

class InventarioController extends Controller
{
    public function index()
    {
        $agrupado = Deteccion::select('objeto_id', DB::raw('SUM(cantidad_detectada) as total'))
            ->groupBy('objeto_id')
            ->with('objeto')
            ->get()
            ->map(function ($item) {
                $estado = match (true) {
                    $item->total < 10 => 'Crítico',
                    $item->total < 30 => 'Bajo',
                    default => 'Suficiente'
                };

                $prioridad = match ($estado) {
                    'Crítico' => 'Alta',
                    'Bajo' => 'Media',
                    'Suficiente' => 'Baja'
                };

                return [
                    'nombre'     => $item->objeto->nombre,
                    'color'      => $item->objeto->color,
                    'total'      => $item->total,
                    'estado'     => $estado,
                    'prioridad'  => $prioridad
                ];
            });

        return view('inventario.inventario', compact('agrupado'));
    }
}
