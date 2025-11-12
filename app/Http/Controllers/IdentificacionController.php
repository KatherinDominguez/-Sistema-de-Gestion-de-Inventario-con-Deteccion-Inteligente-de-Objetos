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
            // Verificar que haya un archivo subido
            $archivo = session('archivo_subido');
            if (!$archivo) {
                $this->agregarAlHistorialTopbar("Error: No se encontró archivo para procesar.");
                return back()->withErrors('No se encontró ningún archivo cargado.');
            }

            $ruta_absoluta = storage_path('app/public/' . $archivo);
            if (!file_exists($ruta_absoluta)) {
                return back()->withErrors('El archivo subido no existe en el servidor.');
            }

            // Obtener TODOS los objetos registrados
            $objetos = Objeto::all();
            if ($objetos->isEmpty()) {
                return back()->withErrors('No hay objetos registrados para identificar.');
            }

            $resultados = [];
            $this->agregarAlHistorialTopbar("Analizando imagen '$archivo' para " . $objetos->count() . " tipos de objetos...");

            foreach ($objetos as $objeto) {
                $tipo = $objeto->forma;
                $color = $objeto->color;
                $nombre = $objeto->nombre;

                $script = base_path('app/Python/detectar_objetos.py');
                $comando = "python " . escapeshellcmd($script) . " " .
                        escapeshellarg($ruta_absoluta) . " " .
                        escapeshellarg($tipo) . " " .
                        escapeshellarg($color);

                $salida = shell_exec($comando . " 2>&1");

                if (!$salida || str_starts_with(trim($salida), 'ERROR:')) {
                    $mensajeError = "Error al procesar '$nombre': " . trim($salida);
                    $this->agregarAlHistorialTopbar($mensajeError);
                    $resultados[] = [
                        'objeto_id' => $objeto->id,
                        'nombre' => $nombre,
                        'color' => $color,
                        'resultado' => 'Error: ' . trim($salida),
                        'cantidad' => 0
                    ];
                } else {
                    // Extraer la cantidad del mensaje (ej: "Detectado 3 objetos...")
                    preg_match('/Detectado (\d+) objetos/', $salida, $matches);
                    $cantidad = isset($matches[1]) ? (int)$matches[1] : 0;

                    $this->agregarAlHistorialTopbar("✓ $nombre ($color): $cantidad detectados");
                    $resultados[] = [
                        'objeto_id' => $objeto->id,
                        'nombre' => $nombre,
                        'color' => $color,
                        'resultado' => trim($salida),
                        'cantidad' => $cantidad
                    ];
                }
            }

            // CAMBIO AQUÍ: Solo guardar en sesión permanente, NO usar with()
            session(['resultados_multiples' => $resultados]);

            // CAMBIO AQUÍ: Redirigir sin with() para mantener la sesión
            return redirect()->route('inicio');

        } catch (\Exception $e) {
            $this->agregarAlHistorialTopbar("Excepción en la identificación múltiple: " . $e->getMessage());
            return back()->withErrors('Error en la identificación: ' . $e->getMessage());
        }
    }

    public function guardarEnInventario(Request $request)
    {
    try {
        $resultados = session('resultados_multiples');
        if (!$resultados) {
            return back()->withErrors('No hay resultados para guardar.');
        }

        foreach ($resultados as $res) {
            if ($res['cantidad'] > 0) {
                Deteccion::create([
                    'objeto_id' => $res['objeto_id'],
                    'archivo' => session('archivo_subido'),
                    'cantidad_detectada' => $res['cantidad'],
                    'resultado' => $res['resultado']
                ]);
            }
        }

        $this->agregarAlHistorialTopbar("Guardados " . count($resultados) . " resultados en el inventario.");
        session()->forget(['resultados_multiples']);
        return back()->with('success', 'Resultados guardados en el inventario.');
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
