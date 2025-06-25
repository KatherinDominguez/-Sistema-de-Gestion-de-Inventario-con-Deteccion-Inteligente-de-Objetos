<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Objeto;
use App\Services\ParserService;
class VozController extends Controller
{
    public function procesar(Request $request, ParserService $parser)
    {
        $texto = strtolower($request->input('texto', ''));
        $texto = preg_replace('/[^\p{L}\s]/u', '', $texto);
        $parser->interpretar($texto);
        $accion = session('comando_voz');
        if ($accion === 'subir') {
            return response()->json(['accion' => 'subir']);
        }
        if ($accion === 'identificar') {
            return response()->json([
                'accion' => 'identificar',
                'nombre' => session('nombre'),
                'color' => session('color')
            ]);
        }
        if ($accion === 'reiniciar') {
            return response()->json(['accion' => 'reiniciar']);
        }
        if ($accion === 'redirigir') {
            return response()->json([
                'accion' => 'redirigir',
                'url' => session('url')
            ]);
        }
        return response()->json(['accion' => 'ninguno']);
    }

}
