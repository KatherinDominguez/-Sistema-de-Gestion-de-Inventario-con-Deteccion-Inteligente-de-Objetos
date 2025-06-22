<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Objeto;

class VozController extends Controller
{
    public function procesar(Request $request)
{
    $texto = strtolower($request->input('texto', ''));

    if (str_contains($texto, 'subir')) {
        session(['comando_voz' => 'subir']);
        return response()->json(['accion' => 'subir']);
    }

    if (str_contains($texto, 'identificar')) {
        preg_match('/identificar\s+(\w+)/', $texto, $coincidencias);
        if (isset($coincidencias[1])) {
            $nombrePosible = $coincidencias[1];
            $objeto = \App\Models\Objeto::whereRaw('LOWER(nombre) = ?', [strtolower($nombrePosible)])->first();

            if ($objeto) {
                session([
                    'comando_voz' => 'identificar',
                    'voz_nombre' => $objeto->nombre,
                    'voz_color' => $objeto->color
                ]);

                return response()->json([
                    'accion' => 'identificar',
                    'nombre' => $objeto->nombre,
                    'color' => $objeto->color
                ]);
            }
        }
    }

    session(['comando_voz' => 'ninguno']);
    return response()->json(['accion' => 'ninguno']);
}

}
