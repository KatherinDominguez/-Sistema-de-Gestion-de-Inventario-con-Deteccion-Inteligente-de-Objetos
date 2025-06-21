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
                    $item->total < 3 => 'Crítico',
                    $item->total < 7 => 'Bajo',
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
    public function exportar()
    {
        $inventario = Deteccion::with('objeto')
            ->selectRaw('objeto_id, SUM(cantidad_detectada) as total')
            ->groupBy('objeto_id')
            ->get();

        $contenido = "📦 INVENTARIO GENERAL - " . now()->format('d/m/Y H:i') . "\n\n";

        foreach ($inventario as $item) {
            $objeto = $item->objeto;
            $estado = match (true) {
                $item->total < 10 => 'Crítico',
                $item->total < 30 => 'Bajo',
                default => 'Suficiente',
            };
            $prioridad = match ($estado) {
                'Crítico' => 'Alta',
                'Bajo' => 'Media',
                default => 'Baja'
            };

            $contenido .= "Objeto: {$objeto->nombre}\n";
            $contenido .= "Color: {$objeto->color}\n";
            $contenido .= "Cantidad Detectada: {$item->total}\n";
            $contenido .= "Estado: {$estado}\n";
            $contenido .= "Prioridad: {$prioridad}\n";
            $contenido .= "----------------------------\n\n";
        }

        $contenido .= "(Total objetos: " . count($inventario) . ")\n";

        $nombreArchivo = 'inventario_' . now()->format('Ymd_His') . '.txt';

        return response($contenido)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="' . $nombreArchivo . '"');
    }
}
