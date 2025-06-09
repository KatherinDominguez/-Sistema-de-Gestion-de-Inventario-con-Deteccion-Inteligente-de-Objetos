@extends('layouts.objetos')
@include('components.botones-volver-cancelar')
@section('title', 'Lista de Objetos')

@section('content')
    <h2>Objetos Registrados</h2>
    <a href="{{ route('objetos.create') }}">Crear nuevo objeto</a>
    <ul>
        @foreach ($objetos as $objeto)
            <li>{{ $objeto->nombre }} - {{ $objeto->forma }} - {{ $objeto->color }}</li>
        @endforeach
    </ul>
@endsection
