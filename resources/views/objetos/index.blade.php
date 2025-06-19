@extends('layouts.objetos')
@include('components.botones-volver-cancelar')
@section('title', 'Lista de Objetos')

@section('content')
    <h2>Objetos Registrados</h2>
    <a href="{{ route('objetos.create') }}">Crear nuevo objeto</a>
    <ul>
    @foreach ($objetos as $objeto)
        <li>
            <strong>{{ $objeto->nombre }}</strong> - 
            Forma: {{ $objeto->forma }} - 
            Color: {{ $objeto->color }} - 
            Categoría: {{ $objeto->categoria }}
        </li>
    @endforeach
    </ul>

@endsection
