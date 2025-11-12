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
        // Obtener el archivo más reciente para cada objeto_id
        $deteccionesRecientes = Deteccion::select('objeto_id', DB::raw('MAX(id) as ultima_deteccion_id'))
            ->groupBy('objeto_id')
            ->pluck('ultima_deteccion_id');

        $deteccionesConArchivo = Deteccion::whereIn('id', $deteccionesRecientes)
            ->pluck('archivo', 'objeto_id');

        $agrupado = Deteccion::select('objeto_id', DB::raw('SUM(cantidad_detectada) as total'))
            ->groupBy('objeto_id')
            ->with('objeto')
            ->get()
            ->map(function ($item) use ($deteccionesConArchivo) {
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
                    'prioridad'  => $prioridad,
                    'archivo'    => $deteccionesConArchivo[$item->objeto_id] ?? null
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

        // Crear el nombre del archivo
        $nombreArchivo = 'inventario_' . now()->format('Ymd_His') . '.csv';

        // Definir los encabezados para el CSV
        $encabezados = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"',
        ];

        // Crear el callback para generar el CSV
        $callback = function() use ($inventario) {
            $archivo = fopen('php://output', 'w');
            
            // Agregar BOM para que Excel reconozca UTF-8
            fprintf($archivo, chr(0xEF).chr(0xBB).chr(0xBF));

            // Escribir encabezados de las columnas
            fputcsv($archivo, [
                'Objeto',
                'Forma',
                'Color',
                'Cantidad Total',
                'Estado',
                'Prioridad',
                'Fecha de Exportación'
            ]);

            // Escribir cada fila de datos
            foreach ($inventario as $item) {
                $objeto = $item->objeto;
                
                // Calcular estado
                $estado = match (true) {
                    $item->total < 3 => 'Crítico',
                    $item->total < 7 => 'Bajo',
                    default => 'Suficiente',
                };
                
                // Calcular prioridad
                $prioridad = match ($estado) {
                    'Crítico' => 'Alta',
                    'Bajo' => 'Media',
                    default => 'Baja'
                };

                fputcsv($archivo, [
                    $objeto->nombre,
                    $objeto->forma,
                    $objeto->color,
                    $item->total,
                    $estado,
                    $prioridad,
                    now()->format('d/m/Y H:i:s')
                ]);
            }

            fclose($archivo);
        };

        return response()->stream($callback, 200, $encabezados);
    }
}
