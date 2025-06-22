@extends('layouts.principal')

@section('title', 'Mi Panel Principal')

@section('sidebar')
    <ul>
        <li><a href="{{ route('user') }}">Perfil</a></li>
        <li><a href="{{ route('objetos.index') }}">Gestión de Objetos</a></li>
        <li><a href="{{ route('inventario') }}">Inventario</a></li>
        <li><a href="{{ route('reportes.index') }}">Reportes</a></li>
    </ul>
@endsection

@section('topbar')
    @include('components.topbar')
@endsection

@section('leftbox')
    <p>Opciones</p>

    <!-- Input oculto para subir archivo por comando de voz -->
    <form id="form-subida" action="{{ route('archivo.subir') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" id="archivo" name="archivo" accept="image/*,video/*" style="display:none;" onchange="document.getElementById('form-subida').submit();">
    </form>

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
            <form id="form-identificar" action="{{ route('identificar') }}" method="POST">
                @csrf
                {{-- Mostrar lo que se reconoció por voz --}}
                @if (session('voz_nombre') && session('voz_color'))
                    <p style="margin-bottom:5px;">🗣️ Voz detectó: <strong>{{ session('voz_nombre') }} ({{ session('voz_color') }})</strong></p>
                    <input type="hidden" name="voz_nombre" value="{{ session('voz_nombre') }}">
                    <input type="hidden" name="voz_color" value="{{ session('voz_color') }}">
                @endif

                {{-- Siempre mostrar selector para permitir elegir manualmente --}}
                <label for="objeto_id">Seleccionar objeto registrado:</label><br>
                <select name="objeto_id" id="objeto_id">
                    <option value="">-- Seleccionar objeto --</option>
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

    <button id="btn-voz">🎤 Usar Comando de Voz</button>
    <p id="texto-reconocido" style="margin-top:10px; font-weight: bold;"></p>

    <script>
        function mostrarOpciones() {
            let opciones = document.getElementById('opciones-identificacion');
            opciones.style.display = opciones.style.display === 'none' ? 'block' : 'none';
        }
    </script>
@endsection

<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
document.addEventListener("DOMContentLoaded", function () {
    const botonVoz = document.getElementById("btn-voz");
    const textoReconocido = document.getElementById("texto-reconocido");
    const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();

    recognition.lang = 'es-ES';
    recognition.interimResults = true;

    botonVoz.addEventListener("click", () => {
        recognition.start();
        textoReconocido.innerText = "🎙️ Escuchando...";
    });

    recognition.onresult = function (event) {
        let final = "";
        for (let i = event.resultIndex; i < event.results.length; i++) {
            final += event.results[i][0].transcript;
        }

        textoReconocido.innerHTML = "🗣️ <strong>Dijiste:</strong> " + final;

        if (event.results[0].isFinal) {
            recognition.stop();
            fetch("/voz/procesar", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ texto: final })
            })
            .then(res => res.json())
            .then(data => {
                if (data.accion === "subir") {
                    document.getElementById("archivo")?.click();
                } else if (data.accion === "identificar") {
                    document.getElementById("form-identificar")?.submit();
                } else {
                    textoReconocido.innerHTML += "<br>⚠️ Comando no reconocido.";
                }
            })
            .catch(err => {
                console.error("Error al procesar comando:", err);
                textoReconocido.innerHTML += "<br>❌ Error al enviar el texto.";
            });
        }
    };

    recognition.onerror = function (event) {
        textoReconocido.innerText = "❌ Error: " + event.error;
    };
});
</script>

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
