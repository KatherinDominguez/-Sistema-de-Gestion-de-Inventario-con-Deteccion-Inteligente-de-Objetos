<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Objeto;
use App\Models\Deteccion;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index()
    {
        // 1. Conteo total por categoría
        $conteo = Deteccion::with('objeto')
            ->get()
            ->groupBy(fn($d) => $d->objeto->categoria ?? 'Sin categoría')
            ->map(fn($items) => $items->sum('cantidad_detectada'));

        $categorias = $conteo->keys()->toArray();
        $cantidades = $conteo->values()->toArray();

        // 2. Productos críticos o bajos (agrupados sin repetirse)
        $productosCriticos = Deteccion::with('objeto')
            ->select('objeto_id', DB::raw('SUM(cantidad_detectada) as total'))
            ->groupBy('objeto_id')
            ->havingRaw('total <= 10')
            ->get();

        return view('reportes.index', compact('categorias', 'cantidades', 'productosCriticos'));
    }
}
