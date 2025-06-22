<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Objeto;

class VozController extends Controller
{
    public function procesar(Request $request)
    {
        $texto = strtolower($request->input('texto', ''));

        // Acción para subir archivo
        if (str_contains($texto, 'subir')) {
            session(['comando_voz' => 'subir']);
            return response()->json(['accion' => 'subir']);
        }

        // Acción para identificar
        if (str_contains($texto, 'identificar')) {
            // Extraer palabra después de "identificar"
            preg_match('/identificar\s+(\w+)/', $texto, $coincidencias);

            if (isset($coincidencias[1])) {
                $nombrePosible = $coincidencias[1];

                // Búsqueda insensible a mayúsculas/minúsculas
                $objeto = Objeto::whereRaw('LOWER(nombre) = ?', [strtolower($nombrePosible)])->first();

                if ($objeto) {
                    session([
                        'comando_voz' => 'identificar',
                        'voz_nombre' => $objeto->nombre,
                        'voz_color' => $objeto->color
                    ]);
                    return response()->json(['accion' => 'identificar']);
                } else {
                    session(['comando_voz' => 'ninguno']);
                    return response()->json(['accion' => 'ninguno']);
                }
            }
        }

        // Si no se reconoce ningún comando válido
        session(['comando_voz' => 'ninguno']);
        return response()->json(['accion' => 'ninguno']);
    }
}
