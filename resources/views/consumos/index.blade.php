@extends('layouts.app')

@section('title', 'Consumos')
@section('page-title', 'Consumos')
@section('page-badge', 'Registros')

@section('content')
<div class="page-actions">
    <button type="button" class="btn btn-primary" onclick="openModal('modal-consumo')">
        <i class="ti ti-plus"></i> Registrar consumo
    </button>
</div>

<form method="GET" class="filter-bar">
    <div class="filter-field">
        <label>Vehiculo</label>
        <select name="vehiculo_id">
            <option value="">Todos</option>
            @foreach($vehiculos as $v)
                <option value="{{ $v->id }}" @selected($vehiculoId == $v->id)>{{ $v->codigo }} &mdash; {{ $v->tipo }}</option>
            @endforeach
        </select>
    </div>
    <div class="filter-field">
        <label>Tramo</label>
        <select name="tramo_id">
            <option value="">Todos</option>
            @foreach($tramos as $t)
                <option value="{{ $t->id }}" @selected($tramoId == $t->id)>{{ $t->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="filter-field">
        <label>Desde</label>
        <input type="date" name="desde" value="{{ $desde }}">
    </div>
    <div class="filter-field">
        <label>Hasta</label>
        <input type="date" name="hasta" value="{{ $hasta }}">
    </div>
    <div class="filter-actions">
        <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-filter"></i> Filtrar</button>
        <a href="{{ route('consumos.index') }}" class="btn btn-sm"><i class="ti ti-x"></i> Limpiar</a>
    </div>
    <div style="margin-left:12px;display:flex;align-items:center;gap:8px">
        <label style="font-size:12px;color:var(--text3)">Items por página</label>
        <select name="per_page" onchange="this.form.submit()">
            <option value="10" @selected(($perPage ?? 10) == 10)>10</option>
            <option value="25" @selected(($perPage ?? 10) == 25)>25</option>
            <option value="50" @selected(($perPage ?? 10) == 50)>50</option>
        </select>
    </div>
</form>

{{-- Datalist compartido: sugerencias de operador desde la lista de Trabajadores --}}
<datalist id="trabajadores-list">
    @foreach($trabajadores as $tr)
        <option value="{{ $tr->name }}"></option>
    @endforeach
</datalist>

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-list"></i> Consumos registrados</div>
        <span style="font-size:12px;color:var(--text3)">
            {{ $consumos->total() }} registros &middot; {{ number_format($totalGalones, 1) }} gal
        </span>
    </div>
    @if($consumos->isEmpty())
        <div class="empty" style="padding:40px"><i class="ti ti-database-off"></i>Sin registros aun</div>
    @else
        <div id="consumos-list">
            <div style="padding:0 6px 6px">
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th><th>Vehiculo</th><th>Tipo</th><th>Tramo</th>
                        <th>Galones</th><th>Operador</th><th>Boleta</th><th>Observaciones</th><th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($consumos as $c)
                        <tr>
                            <td>{{ $c->fecha->format('d/m/Y') }}</td>
                            <td><strong>{{ $c->vehiculo->codigo ?? '-' }}</strong></td>
                            <td>@include('partials.chip-tipo', ['tipo' => $c->vehiculo->tipo ?? 'Otro'])</td>
                            <td>{{ $c->tramo->nombre ?? '-' }}</td>
                            <td class="mono">{{ number_format($c->galones, 1) }}</td>
                            <td>{{ $c->operador ?? '-' }}</td>
                            <td>{{ $c->boletas->isNotEmpty() ? $c->boletas->pluck('numero_boleta')->join(', ') : ($c->boleta->numero_boleta ?? '-') }}</td>
                            <td style="color:var(--text2)">{{ $c->observaciones ?? '-' }}</td>
                            <td style="text-align:right;white-space:nowrap">
                                <button type="button" class="btn btn-sm" onclick="openModal('modal-edit-consumo-{{ $c->id }}')" title="Editar">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <form action="{{ route('consumos.destroy', $c) }}" method="POST" style="display:inline"
                                      data-confirm="¿Eliminar este consumo registrado?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"><i class="ti ti-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
            {{ $consumos->links('pagination.custom') }}
        </div>
    @endif
</div>

{{-- MODAL: Registrar consumo --}}
<div class="modal-overlay" id="modal-consumo">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-title"><i class="ti ti-gas-station"></i> Nuevo registro de consumo</div>
            <button type="button" class="modal-close" onclick="closeModal('modal-consumo')"><i class="ti ti-x"></i></button>
        </div>
        @if($vehiculos->isEmpty() || $tramos->isEmpty())
            <div class="modal-body">
                <div class="alert alert-error" style="margin:0">
                    <i class="ti ti-alert-triangle"></i>
                    Necesitas al menos un vehiculo y un tramo antes de registrar consumos.
                </div>
            </div>
        @else
            <form action="{{ route('consumos.store') }}" method="POST" data-confirm="¿Confirmas registrar este consumo?">
                @csrf
                <div class="modal-body">
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
                                <small id="tramo-turno-modal" class="text-muted" style="display:block;font-weight:600">{{ old('tramo_turno') }}</small>
                                <small id="tramo-desc-modal" class="text-muted">{{ old('tramo_descripcion') }}</small>
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
                            <input type="text" name="operador" list="trabajadores-list" autocomplete="off"
                                   value="{{ old('operador') }}" placeholder="Escribe para buscar trabajador...">
                            <span style="font-size:11px;color:var(--text3)">Sugerencias desde los trabajadores registrados.</span>
                            @error('operador') <span class="field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>Observaciones</label>
                            <input type="text" name="observaciones" value="{{ old('observaciones') }}" placeholder="Opcional...">
                            @error('observaciones') <span class="field-error">{{ $message }}</span> @enderror
                        </div>
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
                <div class="modal-foot">
                    <button type="button" class="btn" onclick="closeModal('modal-consumo')">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Registrar consumo</button>
                </div>
            </form>
        @endif
    </div>
</div>

{{-- Modales de edicion (uno por cada fila) --}}
@foreach($consumos as $c)
    @include('partials.modal-edit-consumo', ['c' => $c])
@endforeach

@if($errors->any())
    @push('scripts')
        <script>
            @if(old('_edit_id'))
                openModal('modal-edit-consumo-{{ old('_edit_id') }}');
            @else
                openModal('modal-consumo');
            @endif
        </script>
    @endpush
@endif
<script>
document.addEventListener('DOMContentLoaded', function(){
    const select = document.querySelector('#modal-consumo select[name="tramo_id"]');
    const desc = document.getElementById('tramo-desc-modal');
    const turno = document.getElementById('tramo-turno-modal');
    if(select && desc){
        function update(){
            const opt = select.options[select.selectedIndex];
            desc.textContent = opt ? (opt.dataset.descripcion || '') : '';
            if(turno) turno.textContent = opt ? (opt.dataset.turno || '') : '';
        }
        select.addEventListener('change', update);
        update();
    }
    // para los modales de edición: actualizar descripcion correspondiente
    document.querySelectorAll('.modal-overlay').forEach(function(modal){
        const sel = modal.querySelector('select[name="tramo_id"]');
        if(!sel) return;
        const id = modal.id.replace('modal-edit-consumo-', '');
        const dest = document.getElementById('tramo-desc-edit-' + id);
        if(!dest) return;
        function upd(){
            const o = sel.options[sel.selectedIndex];
            dest.textContent = o ? (o.dataset.descripcion || '') : '';
            const turnoEdit = document.getElementById('tramo-turno-edit-' + id);
            if(turnoEdit) turnoEdit.textContent = o ? (o.dataset.turno || '') : '';
        }
        sel.addEventListener('change', upd);
        upd();
    });
    // agregar/remover filas para modales index
    window.addBoletaRow = function(forId){
        const container = document.getElementById(forId ? ('new-boletas-' + forId) : 'new-boletas');
        if(!container) return;
        const idx = Date.now();
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
    };
    window.removeBoletaRow = function(btn){ const row = btn.closest('.boleta-row'); if(row) row.remove(); };
    window.splitIntoBoletas = function(forId){
        const galInput = document.querySelector((forId ? '#modal-edit-consumo-' + forId : '') + ' input[name="galones"]') || document.querySelector('input[name="galones"]');
        if(!galInput) return;
        const total = parseFloat(galInput.value) || 0;
        if(total <= 0) return alert('Ingresa galones en el consumo primero');
        const parts = parseInt(prompt('¿En cuántas boletas quieres dividir los ' + total + ' galones?', '2')) || 2;
        const share = (total / parts).toFixed(2);
        for(let i=0;i<parts;i++) addBoletaRow(forId);
        const container = document.getElementById(forId ? ('new-boletas-' + forId) : 'new-boletas');
        const rows = container.querySelectorAll('.boleta-row');
        for(let i=rows.length-parts;i<rows.length;i++){
            const g = rows[i].querySelector('input[name*="[galones]"]'); if(g) g.value = share;
        }
    };
    // Filtrar boletas por vehiculo en modal principal y modales de edición
    function applyFilterForSel(vehSel, boletasSel){
        if(!boletasSel) return;
        const vid = vehSel ? vehSel.value : '';
        Array.from(boletasSel.options).forEach(opt=>{
            const ok = !vid || (opt.dataset.vehiculoId == vid);
            opt.style.display = ok ? '' : 'none';
            if(!ok) opt.selected = false;
            opt.disabled = !ok;
        });
    }
    // modal nuevo
    const mVeh = document.querySelector('#modal-consumo select[name="vehiculo_id"]');
    const mBoletas = document.querySelector('#modal-consumo select[name="boletas_existing[]"]');
    if(mVeh && mBoletas){ mVeh.addEventListener('change', ()=>applyFilterForSel(mVeh, mBoletas)); applyFilterForSel(mVeh, mBoletas); }
    // todos los modales de edición incluidos
    document.querySelectorAll('.modal-overlay').forEach(function(modal){
        const vs = modal.querySelector('select[name="vehiculo_id"]');
        const bs = modal.querySelector('select[name="boletas_existing[]"]');
        if(vs && bs){ vs.addEventListener('change', ()=>applyFilterForSel(vs, bs)); applyFilterForSel(vs, bs); }
    });
    // AJAX pagination: interceptar clicks y reemplazar contenedor `#consumos-list`
    document.addEventListener('click', function(e){
        const a = e.target.closest('.pagination a.page-link');
        if(!a) return;
        // solo actuamos dentro del listado de consumos
        if(!document.getElementById('consumos-list')) return;
        e.preventDefault();
        const url = a.href;
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                // extraer #consumos-list del HTML retornado
                const tmp = document.createElement('div'); tmp.innerHTML = html;
                const newList = tmp.querySelector('#consumos-list');
                if(newList){
                    const old = document.getElementById('consumos-list');
                    old.replaceWith(newList);
                }
                window.scrollTo({ top: document.querySelector('.content').offsetTop - 12, behavior: 'smooth' });
            }).catch(()=>{ window.location.href = url; });
    });
});
</script>
@endsection
