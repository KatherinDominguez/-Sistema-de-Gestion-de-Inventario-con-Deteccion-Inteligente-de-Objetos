<?php
use App\Http\Controllers\ObjetoController;
use App\Http\Controllers\IdentificacionController;
use App\Http\Controllers\ArchivoController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('login');
})->name('login');
Route::post('/login', function () {
    Session::forget('archivo_subido');
    return redirect()->route('inicio');
})->name('login');
Route::get('/inicio', function () {
    return view('inicio');
})->name('inicio');
Route::get('/user', function(){
    return view('user');
})->name('user');
Route::get('/user/nuevo', function(){
    return view('nuevoUsuario');
})->name('nuevoUsuario');
Route::get('/user/nuevo/nuevoUsuario', function(){
    return view('listaUsuarios');
})->name('listaUsuarios');

Route::get('/objetos', [ObjetoController::class, 'index'])->name('objetos.index');
Route::get('/objetos/crear', [ObjetoController::class, 'create'])->name('objetos.create');
Route::post('/objetos', [ObjetoController::class, 'store'])->name('objetos.store');

Route::post('/subir', [ArchivoController::class, 'subir'])->name('archivo.subir');
Route::get('/reiniciar', [ArchivoController::class, 'reiniciar'])->name('archivo.reiniciar');


Route::post('/identificar', [IdentificacionController::class, 'identificar'])->name('identificar');

