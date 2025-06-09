@include('components.botones-volver-cancelar')
<!DOCTYPE html>
<html>
<center>
<head>
    <title>GestionUsuario</title>
</head>
<body>
    <h1> Panel de Usuario</h1>
    <a href="{{ route('nuevoUsuario') }}">
    <button type="submit" class="btn btn-secondary">NUEVO</button>
    </a>
    <button type="submit" class="btn btn-secondary">BORRAR</button>
    <button type="submit" class="btn btn-secondary">EDITAR</button>
</body>
</center>
</html>