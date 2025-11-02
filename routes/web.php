<?php
use App\Http\Controllers\ObjetoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\IdentificacionController;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\ArchivoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\VozController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\DashboardController;

// Rutas de autenticación
Route::get('/', [AuthController::class, 'mostrarLogin'])->name('login');
Route::post('/', [AuthController::class, 'procesarLogin'])->name('login.procesar');
Route::post('/logout', function () {
    Session::flush();
    return redirect()->route('login');
})->name('logout');

Route::get('/registro', [AuthController::class, 'mostrarRegistro'])->name('registro');
Route::post('/registro', [AuthController::class, 'guardarUsuario'])->name('registro.guardar');

// Rutas de usuarios (CORREGIDAS - eliminé duplicados)
Route::get('/user', [AuthController::class, 'mostrarUsuarios'])->name('user');
Route::get('/user/nuevo', function(){
    return view('nuevoUsuario');
})->name('nuevoUsuario');
Route::get('/user/nuevo/nuevoUsuario', function(){
    return view('listaUsuarios');
})->name('listaUsuarios');
Route::post('/usuarios/borrar', [AuthController::class, 'borrarUsuario'])->name('usuarios.borrar');
Route::get('/usuarios/editar/{nombre}', [AuthController::class, 'mostrarFormularioEditar'])->name('usuarios.editar.form');
Route::post('/usuarios/editar', [AuthController::class, 'editarUsuario'])->name('usuarios.editar');

// Dashboard (MOVIDA AL INICIO para evitar conflictos)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Rutas principales
Route::get('/inicio', [InicioController::class, 'index'])->name('inicio');

// Objetos
Route::get('/objetos', [ObjetoController::class, 'index'])->name('objetos.index');
Route::get('/objetos/crear', [ObjetoController::class, 'create'])->name('objetos.create');
Route::post('/objetos', [ObjetoController::class, 'store'])->name('objetos.store');

// Archivos
Route::post('/subir', [ArchivoController::class, 'subir'])->name('archivo.subir');
Route::get('/reiniciar', [ArchivoController::class, 'reiniciar'])->name('archivo.reiniciar');

// Identificación e inventario
Route::post('/identificar', [IdentificacionController::class, 'identificar'])->name('identificar');
Route::post('/guardar-inventario', [IdentificacionController::class, 'guardarEnInventario'])->name('guardar.inventario');
Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario');
Route::post('/inventario', [InventarioController::class, 'exportar'])->name('inventario.exportar');

// Reportes
Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');

// Voz
Route::post('/voz/procesar', [VozController::class, 'procesar'])->name('voz.procesar');