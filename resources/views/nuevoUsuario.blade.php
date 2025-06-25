@include('components.botones-volver-cancelar')
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Usuario</title>
    <link rel="stylesheet" href="{{ asset('css/usuarios.css') }}">
</head>
<body>

    <div class="form-container">
        <h1>Registrar Nuevo Usuario</h1>

        @if(session('success'))
            <div style="color: green;">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('registro.guardar') }}">
            @csrf

            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="btn">Agregar</button>
        </form>
    </div>

</body>
</html>
