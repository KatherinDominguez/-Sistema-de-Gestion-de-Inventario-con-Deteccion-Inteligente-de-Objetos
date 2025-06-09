@include('components.botones-volver-cancelar')
<!DOCTYPE html>
<html>
<center> 
<head>
    <title>Nuevo Usuario</title>
</head>
<body>
    <h1>Nuevo Usuario</h1>
        <label for="nombre">Nombre:</label><br>
        <input type="text" id="nombre" name="nombre" required><br><br>
        <label for="password">Contraseña:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        <a href="{{ route('listaUsuarios') }}">
         <button type="submit" class="btn btn-secondary">Agregar</button>
         </a>
</body>
</center>
</html>