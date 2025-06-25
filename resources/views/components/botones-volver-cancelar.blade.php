<div class="botones-nav">
    <form action="{{ url()->previous() }}" method="get" style="display:inline;">
        <button type="submit" class="btn btn-secondary">⬅️ Volver</button>
    </form>

    <form action="{{ route('inicio') }}" method="get" style="display:inline;">
        <button type="submit" class="btn btn-danger">✖️ Cancelar</button>
    </form>
</div>
