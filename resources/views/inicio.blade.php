@extends('layouts.principal')

@section('title', 'Mi Panel Principal')

@section('sidebar')
    <ul>
        <li><a href="{{ route('user') }}">Perfil</a></li>
        <li><a href="{{ route('objetos.index') }}">Gestión de Objetos</a></li>
        <li><a href="{{ route('inventario') }}">Inventario</a></li>
    </ul>
@endsection

@section('topbar')
    @include('components.topbar')
@endsection

@section('leftbox')
     <p>Opciones</p>
    @if (!session('archivo_subido'))
        <form action="{{ route('archivo.subir') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label>Selecciona imagen o video:</label>
            <input type="file" name="archivo" accept="image/*,video/*,gif/*" required>
            <button type="submit">Subir</button>
        </form>
    @else
        <button onclick="mostrarOpciones()">🔍 Identificar</button>
        <div id="opciones-identificacion" style="display:none; margin-top: 10px;">
            <form action="{{ route('identificar') }}" method="POST">
                @csrf
                <label for="objeto_id">Seleccionar objeto registrado:</label><br>
                <select name="objeto_id" id="objeto_id" required>
                    @foreach ($objetos as $objeto)
                        <option value="{{ $objeto->id }}">
                            {{ $objeto->nombre }} ({{ $objeto->forma }}, {{ $objeto->color }})
                        </option>
                    @endforeach
                </select><br><br>

                <button type="submit">Procesar</button>
            </form>
        </div>
        <button onclick="window.location='{{ route('archivo.reiniciar') }}'">Subir otro archivo</button>
    @endif
    <script>
        function mostrarOpciones() {
            let opciones = document.getElementById('opciones-identificacion');
            opciones.style.display = opciones.style.display === 'none' ? 'block' : 'none';
        }
    </script>
@endsection


@section('rightbox')
    <p>Gráfica</p>
    @if (session('archivo_subido'))
        @php
            $archivo = session('archivo_subido');
            $extension = pathinfo($archivo, PATHINFO_EXTENSION);
        @endphp
        @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif','avif']))
            <img src="{{ asset('storage/' . $archivo) }}" alt="Vista previa" width="300">
        @elseif (in_array($extension, ['mp4', 'mov', 'avi', 'webm']))
            <video width="600" height="450" controls>
                <source src="{{ asset('storage/' . $archivo) }}" type="video/{{ $extension }}">
                Tu navegador no soporta videos.
            </video>
        @endif
    @endif
    @if(session('resultado'))
        <form method="POST" action="{{ route('guardar.inventario') }}">
            @csrf
            <input type="hidden" name="objeto_id" value="{{ session('ultimo_objeto_id') }}">
            <input type="hidden" name="cantidad" value="{{ \Illuminate\Support\Str::of(session('resultado'))->match('/(\d+)/') }}">
            <input type="hidden" name="resultado" value="{{ session('resultado') }}">
            <button type="submit">Guardar en inventario</button>
        </form>
    @endif
    @if ($errors->any())
        <div style="margin-top: 10px; background: #ffe0e0; padding: 10px; border-radius: 5px;">
            <strong>Error:</strong> {{ $errors->first() }}
        </div>
    @endif
@endsection

@section('bottombox')
    @include('components.bottombox-estado')
    @include('components.bottombox-resultado')
@endsection
