<div class="topbar-monitor">
    <div class="monitor-header">
        <div class="monitor-title">
            <span class="monitor-icon">🧠</span>
            <h3>Monitoreo en Tiempo Real</h3>
        </div>
        <button class="toggle-monitor" onclick="toggleMonitor()">
            <span id="toggle-icon">▼</span>
        </button>
    </div>
    
    <div class="monitor-content" id="monitor-content">
        @if(session('historial_topbar') && count(session('historial_topbar')) > 0)
            <div class="notifications-list">
                @foreach(session('historial_topbar') as $registro)
                    @php
                        // Detectar el tipo de notificación
                        $tipo = 'info';
                        $icono = '🔹';
                        
                        if (str_contains($registro, 'Error') || str_contains($registro, '❌')) {
                            $tipo = 'error';
                            $icono = '❌';
                        } elseif (str_contains($registro, '✓') || str_contains($registro, 'Guardado')) {
                            $tipo = 'success';
                            $icono = '✅';
                        } elseif (str_contains($registro, 'Analizando') || str_contains($registro, 'Procesando')) {
                            $tipo = 'processing';
                            $icono = '⏳';
                        } elseif (str_contains($registro, 'Archivo subido')) {
                            $tipo = 'upload';
                            $icono = '📤';
                        }
                        
                        // Extraer fecha/hora y mensaje
                        $partes = explode(' — ', $registro, 2);
                        $fecha = $partes[0] ?? '';
                        $mensaje = $partes[1] ?? $registro;
                    @endphp
                    
                    <div class="notification-item notification-{{ $tipo }}" data-type="{{ $tipo }}">
                        <div class="notification-icon">{{ $icono }}</div>
                        <div class="notification-body">
                            <div class="notification-message">{{ $mensaje }}</div>
                            <div class="notification-time">{{ $fecha }}</div>
                        </div>
                        <div class="notification-indicator"></div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="no-notifications">
                <span class="no-notif-icon">📭</span>
                <p>No hay actividad reciente</p>
            </div>
        @endif
    </div>
</div>

<style>
.topbar-monitor {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    overflow: hidden;
    margin-bottom: 20px;
}

.monitor-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
}

.monitor-title {
    display: flex;
    align-items: center;
    gap: 12px;
}

.monitor-icon {
    font-size: 28px;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.monitor-title h3 {
    margin: 0;
    color: white;
    font-size: 18px;
    font-weight: 600;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.toggle-monitor {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 16px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.toggle-monitor:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.1);
}

#toggle-icon {
    transition: transform 0.3s ease;
}

.toggle-monitor.collapsed #toggle-icon {
    transform: rotate(-90deg);
}

.monitor-content {
    max-height: 400px;
    overflow-y: auto;
    padding: 15px;
    transition: all 0.3s ease;
}

.monitor-content.collapsed {
    max-height: 0;
    padding: 0 15px;
    overflow: hidden;
}

.notifications-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.notification-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 15px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border-left: 4px solid #3498db;
    animation: slideIn 0.3s ease;
    position: relative;
    overflow: hidden;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.notification-success {
    border-left-color: #27ae60;
    background: linear-gradient(to right, #e8f8f5 0%, white 100%);
}

.notification-error {
    border-left-color: #e74c3c;
    background: linear-gradient(to right, #fadbd8 0%, white 100%);
}

.notification-processing {
    border-left-color: #f39c12;
    background: linear-gradient(to right, #fef5e7 0%, white 100%);
}

.notification-upload {
    border-left-color: #9b59b6;
    background: linear-gradient(to right, #f4ecf7 0%, white 100%);
}

.notification-icon {
    font-size: 24px;
    flex-shrink: 0;
}

.notification-body {
    flex: 1;
    min-width: 0;
}

.notification-message {
    font-size: 14px;
    color: #2c3e50;
    font-weight: 500;
    margin-bottom: 4px;
    word-wrap: break-word;
}

.notification-time {
    font-size: 12px;
    color: #7f8c8d;
    font-weight: 400;
}

.notification-indicator {
    position: absolute;
    top: 0;
    right: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(to bottom, rgba(52, 152, 219, 0.5), transparent);
}

.no-notifications {
    text-align: center;
    padding: 40px 20px;
    color: white;
}

.no-notif-icon {
    font-size: 48px;
    display: block;
    margin-bottom: 10px;
    opacity: 0.7;
}

.no-notifications p {
    margin: 0;
    font-size: 16px;
    opacity: 0.8;
}

/* Scrollbar personalizado */
.monitor-content::-webkit-scrollbar {
    width: 8px;
}

.monitor-content::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}

.monitor-content::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 10px;
}

.monitor-content::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}

/* Responsive */
@media (max-width: 768px) {
    .monitor-title h3 {
        font-size: 16px;
    }
    
    .notification-item {
        padding: 10px 12px;
    }
    
    .notification-icon {
        font-size: 20px;
    }
    
    .notification-message {
        font-size: 13px;
    }
}
</style>

<script>
function toggleMonitor() {
    const content = document.getElementById('monitor-content');
    const button = document.querySelector('.toggle-monitor');
    const icon = document.getElementById('toggle-icon');
    
    content.classList.toggle('collapsed');
    button.classList.toggle('collapsed');
    
    if (content.classList.contains('collapsed')) {
        icon.textContent = '▶';
    } else {
        icon.textContent = '▼';
    }
}
</script>