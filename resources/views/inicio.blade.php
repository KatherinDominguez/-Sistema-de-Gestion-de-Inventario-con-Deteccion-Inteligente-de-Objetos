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

    <!-- Subida de archivo por voz o gesto -->
    <form id="form-subida" action="{{ route('archivo.subir') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" id="archivo" name="archivo" accept="image/*,video/*" style="display:none;" onchange="document.getElementById('form-subida').submit();">
    </form>

    @if (!session('archivo_subido'))
        <form id="form-manual" action="{{ route('archivo.subir') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label for="archivo-manual">Selecciona imagen o video:</label>
            <input id="archivo-manual" type="file" name="archivo" accept="image/*,video/*,gif/*" required>
            <button type="submit">Subir</button>
        </form>
    @else
        <button id="btn-identificar" onclick="mostrarOpciones()">🔍 Identificar</button>
        <div id="opciones-identificacion" style="display:none; margin-top: 10px;">
            <form id="form-identificar" action="{{ route('identificar') }}" method="POST">
                @csrf
                <input type="hidden" name="voz_nombre" id="voz_nombre" value="{{ session('voz_nombre') }}">
                <input type="hidden" name="voz_color" id="voz_color" value="{{ session('voz_color') }}">

                @if (session('voz_nombre') && session('voz_color'))
                    <p>🗣️ Voz detectó: <strong>{{ session('voz_nombre') }} ({{ session('voz_color') }})</strong></p>
                @endif

                <label for="objeto_id">Seleccionar objeto registrado:</label><br>
                <select name="objeto_id" id="objeto_id">
                    <option value="">-- Seleccionar objeto --</option>
                    @foreach ($objetos as $objeto)
                        <option value="{{ $objeto->id }}">{{ $objeto->nombre }} ({{ $objeto->forma }}, {{ $objeto->color }})</option>
                    @endforeach
                </select><br><br>

                <button type="submit">Procesar</button>
            </form>
        </div>
        <button onclick="window.location='{{ route('archivo.reiniciar') }}'">Subir otro archivo</button>
    @endif

    <!-- Control por Voz -->
    <button id="btn-voz">🎤 Usar Comando de Voz</button>
    <div id="comandos-voz" style="display:none; font-size: 0.9em; color: #444; margin-top:5px;">
        <strong>Comandos disponibles:</strong>
        <ul>
            <li>🗣️ "subir archivo"</li>
            <li>🗣️ "identificar [nombre] [color]"</li>
        </ul>
    </div>
    <p id="texto-reconocido" style="margin-top:10px; font-weight: bold;"></p>

    <!-- Control por Gestos -->
    <button id="btn-gesto">🖐️ Usar Gestos</button>
    <div id="comandos-gesto" style="display:none; font-size: 0.9em; color: #444; margin-top:5px;">
        <strong>Gestos disponibles:</strong>
        <ul>
            <li>🖐️ Mano abierta (Subir archivo)</li>
            <li>✊ Puño cerrado (Mostrar opciones de identificación)</li>
        </ul>
    </div>
    <div id="contenedor-gesto" style="display:none; position:relative;">
        <video id="video" width="300" height="225" autoplay muted style="border:2px solid black;"></video>
        <canvas id="canvas-gesto" width="300" height="225" style="position:absolute; left:0; top:0;"></canvas>
        <p id="estado-gesto" style="font-weight:bold; color:blue;">🧠 Detectando gestos...</p>
    </div>

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

<!-- Scripts -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const botonVoz = document.getElementById("btn-voz");
    const textoReconocido = document.getElementById("texto-reconocido");
    const comandosVoz = document.getElementById("comandos-voz");
    const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();

    recognition.lang = 'es-ES';
    recognition.interimResults = true;

    botonVoz.addEventListener("click", () => {
        comandosVoz.style.display = 'block';
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
                    "X-CSRF-TOKEN": document.querySelector('meta[name=\"csrf-token\"]').content
                },
                body: JSON.stringify({ texto: final })
            })
            .then(res => res.json())
            .then(data => {
                if (data.accion === "subir") {
                    document.getElementById("archivo")?.click();
                } else if (data.accion === "identificar") {
                    document.getElementById("voz_nombre").value = data.nombre || "";
                    document.getElementById("voz_color").value = data.color || "";
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

<script>
document.addEventListener("DOMContentLoaded", function () {
    const btnGesto = document.getElementById("btn-gesto");
    const video = document.getElementById("video");
    const canvas = document.getElementById("canvas-gesto");
    const ctx = canvas.getContext("2d");
    const estado = document.getElementById("estado-gesto");
    const contenedor = document.getElementById("contenedor-gesto");
    const comandosGesto = document.getElementById("comandos-gesto");

    let gestureStartTime = null;
    let currentGesture = null;
    let camera = null;
    let mediaStream = null;

    btnGesto.addEventListener("click", async () => {
        contenedor.style.display = "block";
        comandosGesto.style.display = "block";

        const hands = new Hands({
            locateFile: (file) => `https://cdn.jsdelivr.net/npm/@mediapipe/hands/${file}`
        });

        hands.setOptions({
            modelComplexity: 1,
            selfieMode: true,
            maxNumHands: 1,
            minDetectionConfidence: 0.7,
            minTrackingConfidence: 0.7
        });

        hands.onResults(onResults);

        mediaStream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = mediaStream;
        await video.play();

        camera = new Camera(video, {
            onFrame: async () => {
                await hands.send({ image: video });
            },
            width: 300,
            height: 225
        });
        camera.start();
    });

    function detenerCamara() {
        if (camera) camera.stop();
        if (mediaStream) {
            mediaStream.getTracks().forEach(track => track.stop());
        }
        contenedor.style.display = "none";
        comandosGesto.style.display = "none";
    }

    function onResults(results) {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.save();
        ctx.scale(-1, 1);
        ctx.translate(-canvas.width, 0);

        if (!results.multiHandLandmarks || results.multiHandLandmarks.length === 0) {
            ctx.restore();
            currentGesture = null;
            gestureStartTime = null;
            estado.innerText = "✋ Esperando gesto...";
            return;
        }

        for (const landmarks of results.multiHandLandmarks) {
            drawConnectors(ctx, landmarks, HAND_CONNECTIONS, { color: '#00FF00', lineWidth: 2 });
            drawLandmarks(ctx, landmarks, { color: '#FF0000', radius: 3 });

            const fingers = [
                { tip: 8, pip: 6 },
                { tip: 12, pip: 10 },
                { tip: 16, pip: 14 },
                { tip: 20, pip: 18 }
            ];

            let openFingers = 0;
            for (let finger of fingers) {
                if (landmarks[finger.tip].y < landmarks[finger.pip].y) {
                    openFingers++;
                }
            }

            const isHandOpen = openFingers >= 3;
            const isFist = openFingers === 0;
            const now = Date.now();

            if (isHandOpen) {
                if (currentGesture !== 'open') {
                    currentGesture = 'open';
                    gestureStartTime = now;
                } else if (now - gestureStartTime >= 3000) {
                    estado.innerText = "🖐️ Gesto detectado: Mano abierta (Subir)";
                    document.getElementById("archivo")?.click();
                    detenerCamara();
                    currentGesture = null;
                } else {
                    estado.innerText = "🕒 Mantén la mano abierta...";
                }
            } else if (isFist) {
                if (currentGesture !== 'fist') {
                    currentGesture = 'fist';
                    gestureStartTime = now;
                } else if (now - gestureStartTime >= 3000) {
                    estado.innerText = "✊ Gesto detectado: Puño cerrado (Identificar)";
                    document.querySelector("button[onclick='mostrarOpciones()']")?.click();
                    detenerCamara();
                    currentGesture = null;
                } else {
                    estado.innerText = "🕒 Mantén el puño cerrado...";
                }
            } else {
                currentGesture = null;
                gestureStartTime = null;
                estado.innerText = "✋ Esperando gesto...";
            }
        }

        ctx.restore();
    }
});
</script>
