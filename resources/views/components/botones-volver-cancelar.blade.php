<form action="{{ url()->previous() }}" method="get" style="display:inline;">
    <button type="submit" class="btn btn-secondary">Volver</button>
</form>

<form action="{{ route('inicio') }}" method="get" style="display:inline;">
    <button type="submit" class="btn btn-danger">Cancelar</button>
</form>
<style>
    .btn {
        padding: 8px 16px;
        border: none;
        border-radius: 5px;
        margin-right: 10px;
        cursor: pointer;
        font-weight: bold;
        text-decoration: none;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
    }

    .btn:hover {
        opacity: 0.9;
    }
</style>