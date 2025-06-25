<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function mostrarLogin() {
        return view('login');
    }

    public function mostrarRegistro() {
        return view('registro');
    }

    public function guardarUsuario(Request $request) {
        $request->validate([
            'nombre' => 'required',
            'password' => 'required',
        ]);

        $linea = $request->nombre . '|' . $request->password . PHP_EOL;

        Storage::append('usuarios.txt', $linea); // Guarda en storage/app/usuarios.txt

        return redirect()->route('login')->with('success', 'Usuario registrado correctamente');
    }

    public function procesarLogin(Request $request) {
        $request->validate([
            'nombre' => 'required',
            'password' => 'required',
        ]);

        $usuarios = Storage::get('usuarios.txt');
        $lineas = explode(PHP_EOL, $usuarios);

        foreach ($lineas as $linea) {
            if (trim($linea) === '' || strpos($linea, '|') === false) {
                continue;
            }
            [$nombre, $password] = explode('|', $linea);

            if ($nombre === $request->nombre && $password === $request->password) {
                session(['usuario' => $nombre]);
                return redirect('/inicio');
            }
        }
        return back()->withErrors(['login' => 'Credenciales incorrectas']);
    }
    public function guardarNuevoUsuario(Request $request) {
        $request->validate([
            'nombre' => 'required',
            'password' => 'required',
        ]);

        $linea = $request->nombre . '|' . $request->password . PHP_EOL;

        Storage::append('usuarios.txt', $linea);

        return redirect()->back()->with('success', 'Usuario registrado correctamente');
    }

    public function mostrarUsuarios()
    {
        $usuarios = [];
        if (Storage::exists('usuarios.txt')) {
            $contenido = Storage::get('usuarios.txt');
            $lineas = explode(PHP_EOL, $contenido);

            foreach ($lineas as $linea) {
                if (trim($linea) === '' || strpos($linea, '|') === false) {
                    continue;
                }
                [$nombre, $password] = explode('|', $linea);
                $usuarios[] = $nombre;
            }
        }
        return view('user', ['usuarios' => $usuarios]);
    }
    public function borrarUsuario(Request $request)
    {
        $nombreAEliminar = $request->input('nombre');

        if (!Storage::exists('usuarios.txt')) {
            return back()->withErrors('Archivo no encontrado');
        }

        $usuarios = explode(PHP_EOL, Storage::get('usuarios.txt'));
        $usuariosFiltrados = [];

        foreach ($usuarios as $linea) {
            if (trim($linea) === '') continue;
            if (strpos($linea, '|') === false) continue;

            [$nombre, $password] = explode('|', $linea);
            if ($nombre !== $nombreAEliminar) {
                $usuariosFiltrados[] = $linea;
            }
        }

        Storage::put('usuarios.txt', implode(PHP_EOL, $usuariosFiltrados) . PHP_EOL);

        return redirect()->route('user')->with('success', 'Usuario eliminado correctamente');
    }
    public function mostrarFormularioEditar($nombre)
    {
        if (!Storage::exists('usuarios.txt')) {
            abort(404, "Archivo no encontrado");
        }

        $usuarios = explode(PHP_EOL, Storage::get('usuarios.txt'));
        $usuarioEncontrado = null;

        foreach ($usuarios as $linea) {
            if (trim($linea) === '') continue;
            if (strpos($linea, '|') === false) continue;

            [$userNombre, $password] = explode('|', $linea);
            if ($userNombre === $nombre) {
                $usuarioEncontrado = ['nombre' => $userNombre, 'password' => $password];
                break;
            }
        }

        if (!$usuarioEncontrado) {
            abort(404, "Usuario no encontrado");
        }

        return view('editarUsuario', ['usuario' => $usuarioEncontrado]);
    }
    public function editarUsuario(Request $request)
    {
        $request->validate([
            'nombre_original' => 'required',
            'nombre' => 'required',
            'password' => 'required',
        ]);

        $nombreOriginal = $request->input('nombre_original');
        $nuevoNombre = $request->input('nombre');
        $nuevaPassword = $request->input('password');

        if (!Storage::exists('usuarios.txt')) {
            return back()->withErrors('Archivo no encontrado');
        }

        $usuarios = explode(PHP_EOL, Storage::get('usuarios.txt'));
        $usuariosActualizados = [];

        foreach ($usuarios as $linea) {
            if (trim($linea) === '') continue;
            if (strpos($linea, '|') === false) continue;

            [$nombre, $password] = explode('|', $linea);

            if ($nombre === $nombreOriginal) {
                $usuariosActualizados[] = $nuevoNombre . '|' . $nuevaPassword;
            } else {
                $usuariosActualizados[] = $linea;
            }
        }

        Storage::put('usuarios.txt', implode(PHP_EOL, $usuariosActualizados) . PHP_EOL);

        return redirect()->route('user')->with('success', 'Usuario modificado correctamente');
    }
}
