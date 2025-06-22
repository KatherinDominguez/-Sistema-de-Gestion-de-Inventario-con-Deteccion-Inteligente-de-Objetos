@extends('layouts.objetos')
@include('components.botones-volver-cancelar')

@section('title', 'Objetos Registrados')

@section('content')
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="color: #2c3e50;">📦 Lista de Objetos Registrados</h2>
        <a href="{{ route('objetos.create') }}" 
           style="background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
           ➕ Crear nuevo objeto
        </a>
    </div>

    @if ($objetos->isEmpty())
        <p style="text-align:center; color: gray;">No hay objetos registrados todavía.</p>
    @else
        <div style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: center;">
            @foreach ($objetos as $objeto)
                <div style="
                    background-color: #f8f9fa; 
                    border: 1px solid #dee2e6; 
                    border-radius: 8px; 
                    padding: 15px; 
                    width: 250px; 
                    box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    <h4 style="color: #343a40; margin-bottom: 10px;">🔹 {{ $objeto->nombre }}</h4>
                    <p><strong>Forma:</strong> {{ ucfirst($objeto->forma) }}</p>
                    <p><strong>Color:</strong> {{ ucfirst($objeto->color) }}</p>
                    <p><strong>Categoría:</strong> {{ ucfirst($objeto->categoria) }}</p>
                </div>
            @endforeach
        </div>
    @endif
@endsection
