<div>
    <strong>🧠 Monitoreo</strong>
    <div style="margin-top: 5px;">
        @if (session('historial_topbar'))
            @foreach (session('historial_topbar') as $linea)
                <div style="margin-bottom: 3px;">🔹 {{ $linea }}</div>
            @endforeach
        @else
            <div>Sin actividad reciente.</div>
        @endif
    </div>
</div>
