@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Perfil</h1>
    <p>Configuración del perfil - Vista temporal para pruebas</p>
</div>
@endsection
    <table style="width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <th style="padding: 10px; text-align: left;">Objeto</th>
                <th style="padding: 10px; text-align: right;">Detecciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topObjetos as $objeto)
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 10px;">{{ $objeto->nombre }}</td>
                <td style="padding: 10px; text-align: right;">{{ $objeto->total_detecciones }}</td>
            </tr>
            @endforeach
        </tbody>
    </table> 
