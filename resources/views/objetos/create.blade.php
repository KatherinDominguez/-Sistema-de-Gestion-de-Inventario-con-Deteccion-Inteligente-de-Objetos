@extends('layouts.objetos')
@include('components.botones-volver-cancelar')

@section('title', 'Crear Objeto')

@section('content')
    <div style="max-width: 600px; margin: 0 auto;">
        <h2 style="text-align: center; color: #2c3e50;">🛠️ Crear Nuevo Objeto</h2>

        <form method="POST" action="{{ route('objetos.store') }}" 
              style="background-color: #f9f9f9; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
            @csrf

            <div style="margin-bottom: 15px;">
                <label for="nombre" style="font-weight: bold;">Nombre del Objeto:</label><br>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required
                    style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="forma" style="font-weight: bold;">Forma Geométrica:</label><br>
                <select name="forma" id="forma" required
                    style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
                    <option value="cuadrado">⬛ Cuadrado</option>
                    <option value="rectangular">⬜ Rectangular</option>
                    <option value="cilindrico">🧃 Cilindrico</option>
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="color" style="font-weight: bold;">Color Dominante:</label><br>
                <select name="color" id="color" required
                    style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
                    <option value="rojo">🔴 Rojo</option>
                    <option value="amarillo">🟡 Amarillo</option>
                    <option value="azul">🔵 Azul</option>
                    <option value="verde">🟢 Verde</option>
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="categoria" style="font-weight: bold;">Categoría Funcional:</label><br>
                <input type="text" name="categoria" id="categoria" value="{{ old('categoria') }}" required
                    placeholder="Ej: Enlatados, Bebidas, Limpieza..."
                    style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
            </div>

            <div style="text-align: center;">
                <button type="submit" 
                    style="background-color: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
                    ✅ Guardar Objeto
                </button>
            </div>
        </form>
    </div>
@endsection
