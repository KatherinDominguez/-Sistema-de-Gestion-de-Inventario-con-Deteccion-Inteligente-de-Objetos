<div style="font-size: 14px; padding: 8px 16px;">
    <strong>🔧 Estado del Sistema</strong><br>
    
    👤 <strong>Usuario:</strong> {{ session('usuario', 'Invitado') }}<br>
    🕒 <strong>Fecha/Hora:</strong> {{ now()->format('d/m/Y H:i') }}<br>

    ⚙️ <strong>Estado:</strong>
    @if (session('archivo_subido') && !session('resultado'))
        Procesando archivo...
    @elseif (session('resultado'))
        Listo: {{ session('resultado') }}
    @else
        Esperando archivo...
    @endif
</div>
