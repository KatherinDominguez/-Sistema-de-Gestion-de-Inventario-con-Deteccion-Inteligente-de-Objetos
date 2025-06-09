<?php
namespace App\Http\Controllers;
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

            return redirect()->route('inicio');
        } catch (\Exception $e) {
            return back()->withErrors(['archivo' => 'Error al subir el archivo: ' . $e->getMessage()]);
        }
    }

    public function reiniciar()
    {
        session()->forget('archivo_subido');
        return redirect()->route('inicio');
    }
}
