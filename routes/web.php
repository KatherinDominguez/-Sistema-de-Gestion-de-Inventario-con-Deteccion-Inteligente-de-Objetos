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
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;


// Ruta principal - redirige al dashboard si está autenticado, sino al login
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');

});

// Cargar rutas de autenticación de Breeze (ESTAS SON LAS QUE NECESITAN LAS PRUEBAS)
require __DIR__.'/auth.php';

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    
    // Dashboard - CON EL NOMBRE QUE ESPERAN LAS PRUEBAS
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Rutas de configuración - REQUERIDAS POR LAS PRUEBAS
    Route::get('/settings/profile', function(){
        return view('settings.profile');
    })->name('settings.profile');
    
    Route::get('/settings/password', function(){
        return view('settings.password');
    })->name('settings.password');

    // Tus rutas existentes (mantén las que necesites)
    Route::get('/inicio', [InicioController::class, 'index'])->name('inicio');
    Route::get('/objetos', [ObjetoController::class, 'index'])->name('objetos.index');
    Route::get('/objetos/crear', [ObjetoController::class, 'create'])->name('objetos.create');
    Route::post('/objetos', [ObjetoController::class, 'store'])->name('objetos.store');
    Route::post('/subir', [ArchivoController::class, 'subir'])->name('archivo.subir');
    Route::get('/reiniciar', [ArchivoController::class, 'reiniciar'])->name('archivo.reiniciar');
    Route::post('/identificar', [IdentificacionController::class, 'identificar'])->name('identificar');
    Route::post('/guardar-inventario', [IdentificacionController::class, 'guardarEnInventario'])->name('guardar.inventario');
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario');
    Route::post('/inventario', [InventarioController::class, 'exportar'])->name('inventario.exportar');
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::post('/voz/procesar', [VozController::class, 'procesar'])->name('voz.procesar');

    // Rutas de usuarios (si las necesitas mantener)
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
});