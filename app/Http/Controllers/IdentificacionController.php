<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Objeto;
use App\Models\Deteccion;

class IdentificacionController extends Controller
{
    public function identificar(Request $request)
{
    try {
        $objeto = null;

        if ($request->filled('objeto_id')) {
            $objeto = Objeto::findOrFail($request->input('objeto_id'));
        } elseif ($request->filled('voz_nombre') && $request->filled('voz_color')) {
            $objeto = Objeto::where('nombre', $request->input('voz_nombre'))
                            ->where('color', $request->input('voz_color'))
                            ->first();
            if (!$objeto) {
                return back()->withErrors("No se encontró un objeto con nombre '" . $request->input('voz_nombre') . "' y color '" . $request->input('voz_color') . "'");
            }
        } else {
            return back()->withErrors('No se proporcionó información suficiente para identificar el objeto.');
        }

        $tipo = $objeto->forma;
        $color = $objeto->color;
        session(['ultimo_objeto_id' => $objeto->id]);

        $archivo = session('archivo_subido');
        if (!$archivo) {
            $this->agregarAlHistorialTopbar("Error: No se encontró archivo para procesar.");
            return back()->withErrors('No se encontró ningún archivo cargado.');
        }

        $this->agregarAlHistorialTopbar("Procesando archivo '$archivo'...");
        $ruta_absoluta = storage_path('app/public/' . $archivo);
        $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
        $script = in_array($extension, ['mp4', 'mov', 'avi', 'webm', 'gif']) ?
                  base_path('app/Python/detectar_objetos_video.py') :
                  base_path('app/Python/detectar_objetos.py');

        $comando = "python " . escapeshellcmd($script) . " " .
                   escapeshellarg($ruta_absoluta) . " " .
                   escapeshellarg($tipo) . " " .
                   escapeshellarg($color);

        $salida = shell_exec($comando . " 2>&1");

        if (!$salida || str_starts_with(trim($salida), 'ERROR:')) {
            $this->agregarAlHistorialTopbar("Error al procesar: " . trim($salida));
            return back()->withErrors('Error en el procesamiento: ' . trim($salida));
        }

        $this->agregarAlHistorialTopbar($salida);
        session()->forget(['resultado']);
        return back()->with('resultado', trim($salida));

    } catch (\Exception $e) {
        $this->agregarAlHistorialTopbar("Excepción en la identificación: " . $e->getMessage());
        return back()->withErrors('Error en la identificación: ' . $e->getMessage());
    }
}


    public function guardarEnInventario(Request $request)
    {
        try {
            Deteccion::create([
                'objeto_id' => $request->input('objeto_id') ?? session('ultimo_objeto_id'),
                'archivo' => session('archivo_subido'),
                'cantidad_detectada' => $request->input('cantidad'),
                'resultado' => $request->input('resultado')
            ]);
            $this->agregarAlHistorialTopbar("Guardado en el inventario correctamente.");
            return back()->with('success', 'Detección guardada en el inventario.');
        } catch (\Exception $e) {
            return back()->withErrors('Error al guardar en inventario: ' . $e->getMessage());
        }
    }

    private function agregarAlHistorialTopbar($mensaje)
    {
        $registro = now()->format('d/m H:i') . " — " . $mensaje;
        $historial = session('historial_topbar', []);
        array_unshift($historial, $registro);
        session(['historial_topbar' => array_slice($historial, 0, 5)]);
    }
}
