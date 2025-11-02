@extends('layouts.principal')

@section('title', 'Mi Panel Principal')

@section('sidebar')
    <ul>
        <li><a href="{{ route('user') }}">Perfil</a></li>
        <li><a href="{{ route('objetos.index') }}">Gestión de Objetos</a></li>
        <li><a href="{{ route('inventario') }}">Inventario</a></li>
        <li><a href="{{ route('reportes.index') }}">Reportes</a></li>
        <li><a href="{{ route('dashboard') }}">📊 Dashboard</a></li>
        <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" class="btn btn-danger">Cerrar Sesión</button>
        </form>
    </ul>
@endsection

@section('topbar')
    @include('components.topbar')
@endsection

@section('leftbox')
    <p>Opciones</p>

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
        <button id="btn-reiniciar" onclick="window.location='{{ route('archivo.reiniciar') }}'">Subir otro archivo</button>
    @endif

    <!-- Control por Voz -->
    <button id="btn-voz">🎤 Usar Comando de Voz</button>
    <button id="btn-cancelar-voz" style="display:none;">❌ Cancelar</button>
    <div id="comandos-voz" style="display:none; font-size: 0.9em; color: #444; margin-top:5px;">
        <strong>Comandos disponibles:</strong>
        <ul>
            <li>🗣️ "subir archivo"</li>
            <li>🗣️ "identificar [nombre]"</li>
            <li>🗣️ "reiniciar"</li>
            <li>🗣️ "abrir inventario"</li>
            <li>🗣️ "abrir objetos"</li>
            <li>🗣️ "abrir reportes"</li>
        </ul>
    </div>
    <p id="texto-reconocido" style="margin-top:10px; font-weight: bold;"></p>

    <!-- Control por Gestos -->
    <button id="btn-gesto">🖐️ Usar Gestos</button>
    <button id="btn-cancelar-gesto" style="display:none;">❌ Cancelar Gestos</button>
    <div id="comandos-gesto" style="display:none; font-size: 0.9em; color: #444; margin-top:5px;">
        <strong>Gestos disponibles:</strong>
        <ul>
            <li>🖐️ Mano abierta (Subir archivo)</li>
            <li>✊ Puño cerrado (Mostrar opciones de identificación)</li>
            <li>☝️ 1 dedo (Reiniciar)</li>
            <li>✌️ 2 dedos (Ir a Objetos)</li>
            <li>🤟 3 dedos (Ir a Inventario)</li>
            <li>🖖 4 dedos (Ir a Reportes)</li>
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

    {{-- Resultados de identificación múltiple --}}
    @if(session('resultados_multiples'))
        <div style="margin-top: 20px; padding: 15px; background-color: #e9f7ef; border: 1px solid #28a745; border-radius: 8px;">
            <h3 style="margin-top: 0; color: #155724;">🔍 Resultados de la identificación:</h3>
            <ul style="list-style-type: none; padding-left: 0;">
                @foreach(session('resultados_multiples') as $res)
                    <li style="margin-bottom: 8px; padding: 5px; background-color: #f8f9fa; border-radius: 4px;">
                        <strong>{{ $res['nombre'] }} ({{ $res['color'] }}):</strong> 
                        @if($res['cantidad'] > 0)
                            <span style="color: #28a745;">{{ $res['cantidad'] }} detectado(s)</span>
                        @else
                            <span style="color: #dc3545;">0 detectados</span>
                        @endif
                        @if(str_starts_with($res['resultado'], 'Error'))
                            <br><small style="color: #dc3545;">⚠️ {{ $res['resultado'] }}</small>
                        @endif
                    </li>
                @endforeach
            </ul>
            <form method="POST" action="{{ route('guardar.inventario') }}" style="margin-top: 10px;">
                @csrf
                <button type="submit" style="padding: 8px 16px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    ✅ Guardar todos los resultados en inventario
                </button>
            </form>
        </div>
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


<script>
document.addEventListener("DOMContentLoaded", function () {
    const botonVoz = document.getElementById("btn-voz");
    const botonCancelar = document.getElementById("btn-cancelar-voz");
    const textoReconocido = document.getElementById("texto-reconocido");
    const comandosVoz = document.getElementById("comandos-voz");
    const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();

    recognition.lang = 'es-ES';
    recognition.interimResults = true;

    botonVoz.addEventListener("click", () => {
        comandosVoz.style.display = 'block';
        botonCancelar.style.display = 'inline-block';
        recognition.start();
        textoReconocido.innerText = "🎙️ Escuchando...";
    });
    botonCancelar.addEventListener("click", () => {
        recognition.abort(); // cancela inmediatamente
        textoReconocido.innerText = "⛔ Cancelado por el usuario.";
        botonCancelar.style.display = 'none';
        comandosVoz.style.display = 'none';
    });
    recognition.onend = function () {
        botonCancelar.style.display = 'none';
    };
    recognition.onresult = function (event) {
        let final = "";
        for (let i = event.resultIndex; i < event.results.length; i++) {
            final += event.results[i][0].transcript;
        }

        textoReconocido.innerHTML = "🗣️ <strong>Dijiste:</strong> " + final;

        if (event.results[0].isFinal) {
            recognition.stop();

            final = final.toLowerCase().replace(/[^\p{L}\s]/gu, '');

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
                    const nombre = data.nombre?.toLowerCase();
                    const color = data.color?.toLowerCase();
                    const select = document.getElementById("objeto_id");

                    // Buscar y seleccionar opción en el select
                    if (select && nombre && color) {
                        for (let option of select.options) {
                            const texto = option.textContent.toLowerCase();
                            if (texto.includes(nombre) && texto.includes(color)) {
                                option.selected = true;
                                break;
                            }
                        }
                    }
                    // Rellenar campos ocultos
                    document.getElementById("voz_nombre").value = data.nombre || "";
                    document.getElementById("voz_color").value = data.color || "";
                    // Enviar el formulario
                    document.getElementById("form-identificar")?.submit();
                } else if (data.accion === "reiniciar") {
                    window.location.href = "{{ route('archivo.reiniciar') }}";
                }else if (data.accion === "redirigir" && data.url) {
                    window.location.href = data.url;
                }else{
                    textoReconocido.innerHTML += "<br>❌ Comando no reconocido.";
                }
             
            })
            .catch(err => {
                console.error("Error al procesar comando:", err);
                textoReconocido.innerHTML += "<br>❌ Error al enviar el texto.";
            });
        }
    };

    recognition.onerror = function (event) {
        botonCancelar.style.display = 'none';
        if (event.error === "aborted") {
            textoReconocido.innerText = "⛔ Cancelado por el usuario.";
        } else {
            textoReconocido.innerText = "❌ Error: " + event.error;
        }
    };
});
</script>


<script>
document.addEventListener("DOMContentLoaded", function () {
    const btnGesto = document.getElementById("btn-gesto");
    const btnCancelarGesto = document.getElementById("btn-cancelar-gesto");
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
        btnCancelarGesto.style.display = "inline-block";
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
    btnCancelarGesto.addEventListener("click", () => {
        detenerCamara();
        estado.innerText = "⛔ Detección cancelada por el usuario.";
    });

    function detenerCamara() {
        if (camera) camera.stop();
        if (mediaStream) {
            mediaStream.getTracks().forEach(track => track.stop());
        }
        contenedor.style.display = "none";
        comandosGesto.style.display = "none";
        btnCancelarGesto.style.display = "none";
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

        let openFingers = 0;

        // 🧠 Detectar pulgar (horizontal, para selfieMode=true)
        const isThumbOpen = landmarks[4].x < landmarks[3].x; // mano derecha
        if (isThumbOpen) openFingers++;

        // ✋ Detectar 4 dedos restantes (vertical)
        const fingers = [
            { tip: 8, pip: 6 },   // índice
            { tip: 12, pip: 10 }, // medio
            { tip: 16, pip: 14 }, // anular
            { tip: 20, pip: 18 }  // meñique
        ];

        for (let finger of fingers) {
            if (landmarks[finger.tip].y < landmarks[finger.pip].y) {
                openFingers++;
            }
        }

        const now = Date.now();
        let gesture = null;
        let action = null;
        let mensaje = "";

        if (openFingers === 0) {
            gesture = "Identificar";
            action = () => {
                document.querySelector("button[onclick='mostrarOpciones()']")?.click();
                mensaje = "✊ Gesto detectado: Puño cerrado (Identificar)";
            };
        } else if (openFingers === 1) {
            gesture = "one";
            action = () => {
                window.location.href = "{{ route('archivo.reiniciar') }}";
                mensaje = "☝️ Gesto detectado: 1 dedo (Reiniciar)";
            };
        } else if (openFingers === 2) {
            gesture = "two";
            action = () => {
                window.location.href = "{{ route('objetos.index') }}";
                mensaje = "✌️ Gesto detectado: 2 dedos (Ir a Objetos)";
            };
        } else if (openFingers === 3) {
            gesture = "three";
            action = () => {
                window.location.href = "{{ route('inventario') }}";
                mensaje = "🤟 Gesto detectado: 3 dedos (Ir a Inventario)";
            };
        } else if (openFingers === 4) {
            gesture = "four";
            action = () => {
                window.location.href = "{{ route('reportes.index') }}";
                mensaje = "🖖 Gesto detectado: 4 dedos (Ir a Reportes)";
            };
        } else if (openFingers === 5) {
            gesture = "open";
            action = () => {
                document.getElementById("archivo")?.click();
                mensaje = "🖐️ Gesto detectado: Mano abierta (Subir)";
            };
        }

        if (gesture !== currentGesture) {
            currentGesture = gesture;
            gestureStartTime = now;
        } else if (now - gestureStartTime >= 3000) {
            if (action) {
                action();
                detenerCamara();
                estado.innerText = mensaje;
                currentGesture = null;
            }
        } else {
            estado.innerText = `🕒 Mantén el gesto (${gesture})...`;
        }
    }

    ctx.restore();
}

});
</script>
