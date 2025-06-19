<div>
    <strong>🧾 Resultado de la Identificación</strong><br>

    @if (session('resultado'))
        <div style="margin-top: 5px;">
            📌 <strong>Resultado:</strong> {{ session('resultado') }}
        </div>
    @else
        <div style="margin-top: 5px;">
            No se ha procesado ninguna imagen aún.
        </div>
    @endif
</div>
