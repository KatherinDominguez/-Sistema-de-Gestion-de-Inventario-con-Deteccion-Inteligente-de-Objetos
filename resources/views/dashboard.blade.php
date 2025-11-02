@extends('layouts.principal')

@section('title', 'Dashboard de Inventario')

@section('sidebar')
    <ul>
        <li><a href="{{ route('user') }}">Perfil</a></li>
        <li><a href="{{ route('objetos.index') }}">Gestión de Objetos</a></li>
        <li><a href="{{ route('inventario') }}">Inventario</a></li>
        <li><a href="{{ route('reportes.index') }}">Reportes</a></li>
        <li><a href="{{ route('dashboard') }}" style="background-color: #007bff; color: white; font-weight: bold;">📊 Dashboard</a></li>
        <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" class="btn btn-danger">Cerrar Sesión</button>
        </form>
    </ul>
@endsection

@section('topbar')
    <h2 style="margin: 0;">📊 Dashboard de Análisis</h2>
@endsection

@section('leftbox')
    <p style="font-weight: bold; margin-bottom: 15px;">Estadísticas Rápidas</p>
    
    <div style="background: #007bff; color: white; padding: 15px; border-radius: 8px; margin-bottom: 10px;">
        <div style="font-size: 0.9em;">Total Objetos</div>
        <div style="font-size: 2em; font-weight: bold;">{{ $totalObjetos }}</div>
    </div>

    <div style="background: #28a745; color: white; padding: 15px; border-radius: 8px; margin-bottom: 10px;">
        <div style="font-size: 0.9em;">Total Detecciones</div>
        <div style="font-size: 2em; font-weight: bold;">{{ $totalDetecciones }}</div>
    </div>

    <div style="background: #dc3545; color: white; padding: 15px; border-radius: 8px; margin-bottom: 10px;">
        <div style="font-size: 0.9em;">Objetos Críticos</div>
        <div style="font-size: 2em; font-weight: bold;">{{ $objetosCriticos }}</div>
        <div style="font-size: 0.75em;">Menos de 3 unidades</div>
    </div>

    <div style="background: #ffc107; color: black; padding: 15px; border-radius: 8px; margin-bottom: 10px;">
        <div style="font-size: 0.9em;">Objetos Bajos</div>
        <div style="font-size: 2em; font-weight: bold;">{{ $objetosBajos }}</div>
        <div style="font-size: 0.75em;">Entre 3 y 7 unidades</div>
    </div>

    <div style="background: #17a2b8; color: white; padding: 15px; border-radius: 8px;">
        <div style="font-size: 0.9em;">Objetos Suficientes</div>
        <div style="font-size: 2em; font-weight: bold;">{{ $objetosSuficientes }}</div>
        <div style="font-size: 0.75em;">Más de 7 unidades</div>
    </div>
@endsection

@section('rightbox')
    <p style="font-weight: bold; margin-bottom: 15px;">🏆 Top 5 Objetos Más Detectados</p>
    
    <table style="width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <thead>
            <tr style="background-color: #343a40; color: white;">
                <th style="padding: 10px; border: 1px solid #dee2e6;">#</th>
                <th style="padding: 10px; border: 1px solid #dee2e6;">Objeto</th>
                <th style="padding: 10px; border: 1px solid #dee2e6;">Cantidad</th>
                <th style="padding: 10px; border: 1px solid #dee2e6;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topObjetos as $index => $item)
            <tr style="text-align: center;">
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold;">{{ $index + 1 }}</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">
                    <span style="background-color: {{ $item->objeto->color }}; color: white; padding: 5px 12px; border-radius: 15px; font-weight: bold;">
                        {{ $item->objeto->nombre }}
                    </span>
                </td>
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold; font-size: 1.2em;">{{ $item->total }}</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">
                    @if($item->total < 3)
                        <span style="background-color: #dc3545; color: white; padding: 5px 12px; border-radius: 15px; font-size: 0.85em; font-weight: bold;">Crítico</span>
                    @elseif($item->total < 7)
                        <span style="background-color: #ffc107; color: black; padding: 5px 12px; border-radius: 15px; font-size: 0.85em; font-weight: bold;">Bajo</span>
                    @else
                        <span style="background-color: #28a745; color: white; padding: 5px 12px; border-radius: 15px; font-size: 0.85em; font-weight: bold;">Suficiente</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="padding: 20px; text-align: center; color: #6c757d;">
                    No hay objetos detectados aún
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 25px;">
        <p style="font-weight: bold; margin-bottom: 10px;">📊 Distribución de Estados</p>
        <canvas id="estadosChart" style="max-height: 250px;"></canvas>
    </div>
@endsection

@section('bottombox')
    <p style="font-weight: bold; margin-bottom: 15px;">📦 Inventario Completo por Objeto</p>
    <canvas id="inventarioChart" style="max-height: 300px;"></canvas>
@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de dona - Estados
    const ctxEstados = document.getElementById('estadosChart')?.getContext('2d');
    if (!ctxEstados) {
        console.error('Canvas estadosChart no encontrado');
        return;
    }
    new Chart(ctxEstados, {
        type: 'doughnut',
        data: {
            labels: ['Críticos', 'Bajos', 'Suficientes'],
            datasets: [{
                data: [{{ $objetosCriticos }}, {{ $objetosBajos }}, {{ $objetosSuficientes }}],
                backgroundColor: ['#dc3545', '#ffc107', '#28a745'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });

    // Gráfico de barras - Inventario
    const ctxInventario = document.getElementById('inventarioChart').getContext('2d');
    new Chart(ctxInventario, {
        type: 'bar',
        data: {
            labels: @json($labels),
            datasets: [{
                label: 'Cantidad Detectada',
                data: @json($cantidades),
                backgroundColor: @json($colores),
                borderColor: @json($colores),
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>