<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Objeto;
use App\Models\Deteccion;

class IdentificacionController extends Controller
{
    public function identificar(Request $request) {
        try {
            $objeto = Objeto::findOrFail($request->input('objeto_id'));
            $tipo = $objeto->nombre;
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

            if (in_array($extension, ['mp4', 'mov', 'avi', 'webm', 'gif'])) {
                $script = base_path('app/Python/detectar_objetos_video.py');
            } else {
                $script = base_path('app/Python/detectar_objetos.py');
            }

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

            // Extraer la cantidad desde el resultado del script
            if (preg_match("/Detectado (\\d+) objetos/i", $salida, $coincidencias)) {
                $cantidad = (int) $coincidencias[1];

                /*Deteccion::create([
                    'objeto_id' => $objeto->id,
                    'archivo' => $archivo,
                    'cantidad_detectada' => $cantidad,
                    'resultado' => trim($salida),
                ]);
                */
            }

            return back()->with('resultado', trim($salida));

        } catch (\Exception $e) {
            $this->agregarAlHistorialTopbar("Excepción en la identificación: " . $e->getMessage());
            return back()->withErrors('Error en la identificación: ' . $e->getMessage());
        }
    }

    private function agregarAlHistorialTopbar($mensaje){
        $registro = now()->format('d/m H:i') . " — " . $mensaje;
        $historial = session('historial_topbar', []);
        array_unshift($historial, $registro); // Agrega al principio
        session(['historial_topbar' => array_slice($historial, 0, 5)]); // Máx 5 entradas
    }
    public function guardarEnInventario(Request $request)
    {
    try {
        Deteccion::create([
            'objeto_id' => $request->input('objeto_id'),
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
}
