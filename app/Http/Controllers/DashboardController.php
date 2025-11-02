<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Objeto;
use App\Models\Deteccion;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Total de objetos registrados
        $totalObjetos = Objeto::count();
        
        // Total de detecciones acumuladas
        $totalDetecciones = Deteccion::sum('cantidad_detectada');
        
        // Agrupar por objeto_id y sumar cantidades
        $inventario = Deteccion::select('objeto_id', DB::raw('SUM(cantidad_detectada) as total'))
            ->groupBy('objeto_id')
            ->with('objeto')
            ->get();
        
        // Contar estados (crítico, bajo, suficiente)
        $objetosCriticos = 0;
        $objetosBajos = 0;
        $objetosSuficientes = 0;
        
        foreach ($inventario as $item) {
            if ($item->total < 3) {
                $objetosCriticos++;
            } elseif ($item->total < 7) {
                $objetosBajos++;
            } else {
                $objetosSuficientes++;
            }
        }
        
        // Top 5 objetos más detectados
        $topObjetos = Deteccion::select('objeto_id', DB::raw('SUM(cantidad_detectada) as total'))
            ->groupBy('objeto_id')
            ->with('objeto')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
        
        // Preparar datos para gráficos
        $labels = [];
        $cantidades = [];
        $colores = [];
        
        foreach ($inventario as $item) {
            $labels[] = $item->objeto->nombre;
            $cantidades[] = $item->total;
            $colores[] = $item->objeto->color;
        }
        
        return view('dashboard', compact(
            'totalObjetos',
            'totalDetecciones',
            'objetosCriticos',
            'objetosBajos',
            'objetosSuficientes',
            'topObjetos',
            'labels',
            'cantidades',
            'colores'
        ));
    }
}