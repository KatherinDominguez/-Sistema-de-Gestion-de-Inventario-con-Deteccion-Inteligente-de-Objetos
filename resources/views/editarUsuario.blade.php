@include('components.botones-volver-cancelar')
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="{{ asset('css/usuarios.css') }}" />
</head>
<body>
    <div class="form-container">
        <h1>Editar Usuario</h1>

        @if(session('success'))
            <div style="color: green;">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('usuarios.editar') }}">
            @csrf

            <input type="hidden" name="nombre_original" value="{{ $usuario['nombre'] }}">

            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="{{ $usuario['nombre'] }}" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" value="{{ $usuario['password'] }}" required>

            <button type="submit" class="btn">Guardar Cambios</button>
        </form>
    </div>
</body>
</html>
