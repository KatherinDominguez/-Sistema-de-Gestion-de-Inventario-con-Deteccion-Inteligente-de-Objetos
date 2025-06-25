@include('components.botones-volver-cancelar')
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="{{ asset('css/usuarios.css') }}"> 
</head>
<body>
    @if(session('success'))
    <div style="color: green;">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div style="color: red;">{{ $errors->first() }}</div>
    @endif

    <h1>Panel de Gestión de Usuarios</h1>

    <div class="botonera">
        <a href="{{ route('nuevoUsuario') }}">
            <button class="btn">➕ Nuevo</button>
        </a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        @forelse($usuarios as $usuario)
            <tr>
                <td>{{ $usuario }}</td>
                <td class="acciones">
                    <a href="{{ route('usuarios.editar.form', ['nombre' => $usuario]) }}">
                        <button type="button" class="btn editar">✏️ Editar</button>
                    </a>
                    <form action="{{ route('usuarios.borrar') }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="hidden" name="nombre" value="{{ $usuario }}">
                        <button type="submit" class="btn">🗑️ Borrar</button>
                    </form>
                 </td>
            </tr>
        @empty
            <tr>
                <td colspan="2">No hay usuarios registrados.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>
