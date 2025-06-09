@extends('layouts.principal')

@section('title', 'Mi Panel Principal')

@section('sidebar')
    <ul>
        <li><a href="{{ route('user') }}">Perfil</a></li>
        <li><a href="{{ route('objetos.index') }}">Gestión de Objetos</a></li>
    </ul>
@endsection

@section('topbar')
            Monitoreo con scroll <br>
            Línea 1 <br>
            Línea 2 <br>
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
                <label for="tipo">Seleccionar tipo de objeto:</label><br>
                <select name="tipo" id="tipo" required>
                    <option value="lata">Latas</option>
                    <option value="botella">Botellas</option>
                    <option value="otro">Otro</option>
                </select><br><br>

                <label for="color">Filtrar por color:</label><br>
                <select name="color" id="color">
                    <option value="">-- Sin filtro --</option>
                    <option value="rojo">Rojo</option>
                    <option value="azul">Azul</option>
                    <option value="verde">Verde</option>
                    <option value="amarillo">Amarillo</option>
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
        @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
            <img src="{{ asset('storage/' . $archivo) }}" alt="Vista previa" width="300">
        @elseif (in_array($extension, ['mp4', 'mov', 'avi', 'webm']))
            <video width="600" height="450" controls>
                <source src="{{ asset('storage/' . $archivo) }}" type="video/{{ $extension }}">
                Tu navegador no soporta videos.
            </video>
        @endif
    @endif
    @if (session('resultado'))
        <div style="margin-top: 10px; background: #e0ffe0; padding: 10px; border-radius: 5px;">
            <strong>Resultado:</strong> {{ session('resultado') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="margin-top: 10px; background: #ffe0e0; padding: 10px; border-radius: 5px;">
            <strong>Error:</strong> {{ $errors->first() }}
        </div>
    @endif
@endsection

@section('bottombox')
    <p>Identificacion</p>
@endsection
