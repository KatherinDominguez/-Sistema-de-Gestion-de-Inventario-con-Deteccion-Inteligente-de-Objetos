<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ParserService;

class VozController extends Controller
{
    public function procesar(Request $request, ParserService $parserService)
    {
        // Normalizamos el texto a minúsculas
        $texto = strtolower($request->input('texto'));
        $parserService->interpretar($texto);
        if (str_contains($texto, 'subir')) {
            session(['comando_voz' => 'subir']);
            return response()->json(['accion' => 'subir']);
        }
        // Acción para identificar
        if (str_contains($texto, 'identificar')) {
            session(['comando_voz' => 'identificar']);

            // Opcional: recuperar palabras como color o tipo
            if (str_contains($texto, 'rojo')) {
                session(['color' => 'rojo']);
            }
            if (str_contains($texto, 'coca')) {
                session(['nombre' => 'coca']);
            }

            return response()->json(['accion' => 'identificar']);
        }

        // Si no se reconoce ninguna acción
        return response()->json(['accion' => 'ninguno']);
    }
}
