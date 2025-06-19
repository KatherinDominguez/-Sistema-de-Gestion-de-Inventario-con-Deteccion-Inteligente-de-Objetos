<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArchivoController extends Controller
{
    public function subir(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi,webm|max:51200',
        ]);

        try {
            $ruta = $request->file('archivo')->store('uploads', 'public');
            session(['archivo_subido' => $ruta]);

            $this->agregarAlHistorialTopbar("Archivo subido: '$ruta'");

            return redirect()->route('inicio');
        } catch (\Exception $e) {
            $this->agregarAlHistorialTopbar("Error al subir archivo: " . $e->getMessage());

            return back()->withErrors(['archivo' => 'Error al subir el archivo: ' . $e->getMessage()]);
        }
    }

    public function reiniciar()
    {
        $archivo = session('archivo_subido');
        session()->forget('archivo_subido');

        if ($archivo) {
            $this->agregarAlHistorialTopbar("Archivo reiniciado: '$archivo' eliminado de sesión.");
        } else {
            $this->agregarAlHistorialTopbar("Intento de reinicio sin archivo activo.");
        }

        return redirect()->route('inicio');
    }

    private function agregarAlHistorialTopbar($mensaje)
    {
        $registro = now()->format('d/m H:i') . " — " . $mensaje;
        $historial = session('historial_topbar', []);
        array_unshift($historial, $registro);
        session(['historial_topbar' => array_slice($historial, 0, 5)]);
    }
}
