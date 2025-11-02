<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario de Objetos</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 40px;
            background-color: #f4f6f9;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }

        table {
            width: 90%;
            margin: 0 auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 14px 22px;
            border: 1px solid #dee2e6;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        .estado-badge, .prioridad-badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: bold;
            display: inline-block;
        }

        .estado-crítico { background-color: #f8d7da; color: #721c24; }
        .estado-bajo { background-color: #fff3cd; color: #856404; }
        .estado-suficiente { background-color: #d4edda; color: #155724; }

        .badge-estado-Crítico    { background-color: #dc3545; color: white; }
        .badge-estado-Bajo       { background-color: #ffc107; color: black; }
        .badge-estado-Suficiente { background-color: #28a745; color: white; }

        .badge-prioridad-Alta    { background-color: #dc3545; color: white; }
        .badge-prioridad-Media   { background-color: #fd7e14; color: white; }
        .badge-prioridad-Baja    { background-color: #17a2b8; color: white; }

        .volver-btn, .exportar-btn {
            display: inline-block;
            margin: 30px auto 0;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            transition: background 0.2s;
        }

        .volver-btn {
            background-color: #007bff;
            color: white;
        }

        .volver-btn:hover {
            background-color: #0056b3;
        }

        .exportar-btn {
            background-color: #6c757d;
            color: white;
            margin-left: 45%;
        }

        .exportar-btn:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <h1>📦 Inventario de Objetos Detectados</h1>

    <table>
        <thead>
            <tr>
                <th>Imagen</th>
                <th>Objeto</th>
                <th>Color</th>
                <th>Total Detectado</th>
                <th>Estado</th>
                <th>Prioridad</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($agrupado as $item)
                @php
                    $estadoClass = 'badge-estado-' . $item['estado'];
                    $prioridadClass = 'badge-prioridad-' . $item['prioridad'];
                @endphp
                <tr>
                    <td>
                        @if($item['archivo'])
                            <img src="{{ asset('storage/' . $item['archivo']) }}" 
                                alt="Miniatura" 
                                style="width: 60px; height: auto; border: 1px solid #ddd; border-radius: 4px;">
                        @else
                            <span style="color: #6c757d;">—</span>
                        @endif
                    </td>
                    <td>{{ $item['nombre'] }}</td>
                    <td>{{ ucfirst($item['color']) }}</td>
                    <td>{{ $item['total'] }}</td>
                    <td><span class="estado-badge {{ $estadoClass }}">{{ $item['estado'] }}</span></td>
                    <td><span class="prioridad-badge {{ $prioridadClass }}">{{ $item['prioridad'] }}</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No hay registros aún.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="text-align: center;">
        <a href="{{ route('inicio') }}" class="volver-btn">⬅ Volver al Panel</a>

        <form action="{{ route('inventario.exportar') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="exportar-btn">📊 Exportar como CSV</button>
        </form>
    </div>
</body>
</html>
