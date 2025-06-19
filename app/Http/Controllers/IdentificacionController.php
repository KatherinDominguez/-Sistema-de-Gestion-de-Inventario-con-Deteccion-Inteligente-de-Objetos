<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class IdentificacionController extends Controller
{
    public function identificar(Request $request) {
    try {
        $tipo = $request->input('tipo');
        $color = $request->input('color');

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

}
