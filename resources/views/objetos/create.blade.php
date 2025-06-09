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
            <label for="color">Color:</label>
            <input type="text" name="color" id="color" value="{{ old('color') }}" required>
        </div>
        <div>
            <button type="submit">Guardar Objeto</button>
        </div>
    </form>
@endsection
