<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <div class="login-container">
        <h1>Iniciar Sesión</h1>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Ingresar</button>
        </form>
            <p>¿No tienes una cuenta? <a href="{{ route('registro') }}">Registrar usuario</a></p>
    </div>
</body>
</html>
