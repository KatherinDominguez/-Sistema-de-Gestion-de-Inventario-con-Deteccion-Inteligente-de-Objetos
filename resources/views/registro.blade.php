<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <div class="login-container">
        <h1>Registro de Usuario</h1>

        <form method="POST" action="{{ route('registro.guardar') }}">
            @csrf
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Registrar</button>
        </form>

        <p>¿Ya tienes una cuenta? <a href="{{ route('login') }}">Volver al login</a></p>
    </div>
</body>
</html>
