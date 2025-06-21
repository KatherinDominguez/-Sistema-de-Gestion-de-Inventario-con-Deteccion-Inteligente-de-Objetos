<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario de Objetos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f8f9fa;
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
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px 20px;
            border: 1px solid #dee2e6;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        .estado-crítico {
            background-color: #f8d7da;
            color: #721c24;
        }

        .estado-bajo {
            background-color: #fff3cd;
            color: #856404;
        }

        .estado-suficiente {
            background-color: #d4edda;
            color: #155724;
        }

        .volver-btn {
            display: block;
            margin: 30px auto 0;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            width: 200px;
        }

        .volver-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>📦 Inventario de Objetos Detectados</h1>

    <table>
        <thead>
            <tr>
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
                    $clase = match ($item['estado']) {
                        'Crítico' => 'estado-crítico',
                        'Bajo' => 'estado-bajo',
                        default => 'estado-suficiente'
                    };
                @endphp
                <tr class="{{ $clase }}">
                    <td>{{ $item['nombre'] }}</td>
                    <td>{{ ucfirst($item['color']) }}</td>
                    <td>{{ $item['total'] }}</td>
                    <td>{{ $item['estado'] }}</td>
                    <td>{{ $item['prioridad'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No hay registros aún.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <a href="{{ route('inicio') }}" class="volver-btn">⬅ Volver al Panel</a>
    <form action="{{ route('inventario.exportar') }}" method="POST">
        @csrf
        <button type="submit">📄 Exportar como TXT</button>
    </form>
</body>
</html>
