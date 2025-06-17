@include('components.botones-volver-cancelar')
@extends('layouts.objetos')

@section('title', 'Crear Objeto')

@section('content')
    <h2>Crear nuevo Objeto</h2>
    <form method="POST" action="{{ route('objetos.store') }}">
        @csrf
        <div>
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required>
        </div>
        <div>
            <label for="forma">Forma:</label>
            <select name="forma" id="forma">
                <option value="circular">Circular</option>
                <option value="cuadrado">Cuadrado</option>
                <option value="rectangular">Rectangular</option>
                <option value="cilíndrico">Cilíndrico</option>
            </select>
        </div>
        <div>
            <label for="Color">Color:</label>
            <select name="color" id="color">
                <option value="rojo">rojo</option>
                <option value="amarillo">amarillo</option>
                <option value="azul">azul</option>
                <option value="verde">verde</option>
            </select>
        </div>
        <div>
            <button type="submit">Guardar Objeto</button>
        </div>
    </form>
@endsection
