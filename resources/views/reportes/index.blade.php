@extends('layouts.reportes')

@section('title', 'Reportes')

@section('content')
<div class="card mb-4">
    <a href="{{ route('inicio') }}" class="btn btn-primary m-3">← Volver al Panel Principal</a>

    <div class="card-body">
        <h4 class="mb-4">
            📊 <strong>Reporte de Conteo por Categoría</strong>
        </h4>
        <canvas id="grafico-categorias" height="150"></canvas>
        <hr>

        <h4 class="mt-5 mb-3">🚨 <strong>Productos en estado crítico o bajo</strong></h4>
        <table class="table table-bordered text-center">
            <thead class="bg-light">
                <tr>
                    <th>Nombre</th>
                    <th>Cantidad Detectada</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($productosCriticos as $item)
                    <tr style="background-color: #ffd6d6;">
                        <td>{{ $item->objeto->nombre }}</td>
                        <td>{{ $item->total }}</td>
                        <td>{{ $item->total <= 5 ? 'Crítico' : 'Bajo' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">No hay productos en estado crítico o bajo.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('grafico-categorias').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($categorias) !!},
            datasets: [{
                label: 'Cantidad por categoría',
                data: {!! json_encode($cantidades) !!},
                backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545'],
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    precision: 0
                }
            }
        }
    });
</script>
@endsection
