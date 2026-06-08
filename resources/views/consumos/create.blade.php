@extends('layouts.app')

@section('title', 'Registrar Consumo')
@section('page-title', 'Registrar consumo')
@section('page-badge', 'Nuevo registro')

@section('content')
<div style="max-width:760px">
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="ti ti-gas-station"></i> Nuevo registro de consumo</div>
        </div>
        <div class="card-body">
            @if($vehiculos->isEmpty() || $tramos->isEmpty())
                <div class="alert alert-error">
                    <i class="ti ti-alert-triangle"></i>
                    Necesitas al menos un vehiculo y un tramo activos antes de registrar consumos.
                </div>
            @endif
            <form action="{{ route('consumos.store') }}" method="POST">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label>Vehiculo</label>
                        <select name="vehiculo_id" required>
                            <option value="">Seleccionar...</option>
                            @foreach($vehiculos as $v)
                                <option value="{{ $v->id }}" @selected(old('vehiculo_id') == $v->id)>
                                    {{ $v->codigo }} &mdash; {{ $v->tipo }}
                                </option>
                            @endforeach
                        </select>
                        @error('vehiculo_id') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Tramo</label>
                        <select name="tramo_id" required>
                            <option value="">Seleccionar...</option>
                            @foreach($tramos as $t)
                                <option value="{{ $t->id }}" data-descripcion="{{ e($t->descripcion) }}" data-turno="{{ e($t->turno) }}" @selected(old('tramo_id') == $t->id)>
                                    {{ $t->nombre }} ({{ rtrim(rtrim(number_format($t->km, 2), '0'), '.') }} km)
                                </option>
                            @endforeach
                        </select>
                        @error('tramo_id') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <small id="tramo-turno" class="text-muted" style="display:block;font-weight:600">{{ old('tramo_turno') }}</small>
                        <small id="tramo-desc" class="text-muted">{{ old('tramo_descripcion') }}</small>
                    </div>
                    <div class="form-group">
                        <label>Galones usados</label>
                        <input type="number" name="galones" value="{{ old('galones') }}" step="0.01" min="0.01" placeholder="0.00" required>
                        @error('galones') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Fecha</label>
                        <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required>
                        @error('fecha') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Operador / Chofer</label>
                        <input type="text" name="operador" value="{{ old('operador') }}" placeholder="Nombre del operador">
                        @error('operador') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Observaciones</label>
                        <input type="text" name="observaciones" value="{{ old('observaciones') }}" placeholder="Opcional...">
                        @error('observaciones') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <hr>
                <h4>Boletas (opcional)</h4>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Seleccionar boletas existentes</label>
                        <select name="boletas_existing[]" multiple style="min-height:90px">
                            @foreach($boletas as $b)
                                <option value="{{ $b->id }}" data-vehiculo-id="{{ $b->vehiculo_id }}" data-placa="{{ optional($b->vehiculo)->placa }}">{{ $b->numero_boleta }} — {{ $b->proveedor }} — {{ optional($b->fecha)->format('d/m/Y') }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Mantén presionada la tecla Ctrl/Cmd para seleccionar varias.</small>
                    </div>
                    <div class="form-group" style="min-width:100%">
                        <label>Nueva(s) boleta(s) (opcional)</label>
                        <div id="new-boletas">
                            <div class="boleta-row" data-index="0">
                                <input type="text" name="new_boletas[0][numero]" placeholder="Número" style="width:22%">
                                <input type="text" name="new_boletas[0][proveedor]" placeholder="Proveedor" style="width:28%">
                                <input type="number" step="0.01" min="0" name="new_boletas[0][galones]" placeholder="Galones" style="width:12%">
                                <input type="number" step="0.01" min="0" name="new_boletas[0][precio]" placeholder="Precio" style="width:12%">
                                <input type="date" name="new_boletas[0][fecha]" style="width:14%">
                                <button type="button" class="btn btn-sm" onclick="removeBoletaRow(this)">Eliminar</button>
                            </div>
                        </div>
                        <div style="margin-top:8px">
                            <button type="button" class="btn" onclick="addBoletaRow()">+ Agregar nueva boleta</button>
                            <button type="button" class="btn" onclick="splitIntoBoletas()">Dividir galones en boletas</button>
                            <small class="text-muted" id="boletas-sum">Suma boletas: 0</small>
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <a href="{{ route('consumos.index') }}" class="btn">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Registrar consumo</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const select = document.querySelector('select[name="tramo_id"]');
    const desc = document.getElementById('tramo-desc');
    function update(){
        const opt = select.options[select.selectedIndex];
        desc.textContent = opt ? (opt.dataset.descripcion || '') : '';
        // mostrar turno si existe
        const turnoEl = document.getElementById('tramo-turno');
        if(turnoEl) turnoEl.textContent = opt ? (opt.dataset.turno || '') : '';
    }
    select.addEventListener('change', update);
    update();
});
</script>
<script>
let boletaIndex = 1;
function addBoletaRow(forId){
    const container = document.getElementById(forId ? ('new-boletas-' + forId) : 'new-boletas');
    if(!container) return;
    const idx = boletaIndex++;
    const row = document.createElement('div');
    row.className = 'boleta-row';
    row.dataset.index = idx;
    row.innerHTML = `
        <input type="text" name="new_boletas[${idx}][numero]" placeholder="Número" style="width:22%">
        <input type="text" name="new_boletas[${idx}][proveedor]" placeholder="Proveedor" style="width:28%">
        <input type="number" step="0.01" min="0" name="new_boletas[${idx}][galones]" placeholder="Galones" style="width:12%">
        <input type="number" step="0.01" min="0" name="new_boletas[${idx}][precio]" placeholder="Precio" style="width:12%">
        <input type="date" name="new_boletas[${idx}][fecha]" style="width:14%">
        <button type="button" class="btn btn-sm" onclick="removeBoletaRow(this)">Eliminar</button>
    `;
    container.appendChild(row);
}
function removeBoletaRow(btn){
    const row = btn.closest('.boleta-row');
    if(row) row.remove();
}
function splitIntoBoletas(forId){
    const galInput = document.querySelector((forId ? '#modal-edit-consumo-' + forId : '') + ' input[name="galones"]') || document.querySelector('input[name="galones"]');
    if(!galInput) return;
    const total = parseFloat(galInput.value) || 0;
    if(total <= 0) return alert('Ingresa galones en el consumo primero');
    // prompt para cantidad de boletas
    const parts = parseInt(prompt('¿En cuántas boletas quieres dividir los ' + total + ' galones?', '2')) || 2;
    const share = (total / parts).toFixed(2);
    for(let i=0;i<parts;i++) addBoletaRow(forId);
    // llenar los campos de galones recién creados
    const container = document.getElementById(forId ? ('new-boletas-' + forId) : 'new-boletas');
    const rows = container.querySelectorAll('.boleta-row');
    for(let i=rows.length-parts;i<rows.length;i++){
        const g = rows[i].querySelector('input[name*="[galones]"]');
        if(g) g.value = share;
    }
}
</script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    // Filtrar boletas existentes según vehiculo seleccionado
    const vehSel = document.querySelector('select[name="vehiculo_id"]');
    const boletasSel = document.querySelector('select[name="boletas_existing[]"]');
    function applyFilter(){
        if(!boletasSel) return;
        const vid = vehSel ? vehSel.value : '';
        Array.from(boletasSel.options).forEach(opt=>{
            const ok = !vid || (opt.dataset.vehiculoId == vid);
            opt.style.display = ok ? '' : 'none';
            if(!ok) opt.selected = false;
            opt.disabled = !ok;
        });
    }
    if(vehSel) vehSel.addEventListener('change', applyFilter);
    applyFilter();
});
</script>
@endsection
