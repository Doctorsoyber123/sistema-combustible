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

                {{-- SECCIÓN BOLETAS --}}
                <div style="padding:0 16px 16px">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
                        <span style="font-size:13px;font-weight:500;display:flex;align-items:center;gap:6px">
                            <i class="ti ti-receipt" style="font-size:16px;color:var(--text2)"></i>
                            Boletas asociadas
                            <span id="boletas-count"
                                  style="font-size:11px;padding:2px 8px;border-radius:20px;background:var(--info-bg,#e6f1fb);color:var(--info,#185fa5)">
                                0
                            </span>
                        </span>
                        <span style="font-size:11px;color:var(--text3)">Opcional</span>
                    </div>

                    <div style="background:var(--surface2,#f8f8f6);border:0.5px solid var(--border);border-radius:var(--radius-lg,12px);padding:12px">

                        {{-- Lista de boletas añadidas --}}
                        <div id="boletas-list" style="display:flex;flex-direction:column;gap:6px">
                            <div id="boletas-empty" style="text-align:center;padding:12px 0;color:var(--text3);font-size:12px">
                                <i class="ti ti-receipt-off" style="font-size:20px;display:block;margin-bottom:4px"></i>
                                Sin boletas aún
                            </div>
                        </div>

                        <div style="height:0.5px;background:var(--border);margin:12px 0"></div>

                        {{-- Botones toggle --}}
                        <div style="display:flex;gap:8px">
                            <button type="button" class="btn" style="flex:1" onclick="toggleBoletaForm('nueva')">
                                <i class="ti ti-plus"></i> Nueva boleta
                            </button>
                            <button type="button" class="btn" style="flex:1" onclick="toggleBoletaForm('existente')">
                                <i class="ti ti-link"></i> Boleta existente
                            </button>
                        </div>

                        {{-- Formulario inline: Nueva boleta --}}
                        <div id="form-boleta-nueva"
                             style="display:none;margin-top:12px;background:var(--surface,#fff);border:0.5px solid var(--border);border-radius:var(--radius,8px);padding:12px">
                            <div style="font-size:11px;font-weight:500;color:var(--text3);text-transform:uppercase;letter-spacing:.04em;margin-bottom:10px">
                                Crear nueva boleta
                            </div>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>Número</label>
                                    <input type="text" id="nb_numero" placeholder="001-00123456">
                                </div>
                                <div class="form-group">
                                    <label>Proveedor</label>
                                    <input type="text" id="nb_proveedor" placeholder="Petroperu, Primax…">
                                </div>
                                <div class="form-group">
                                    <label>Galones</label>
                                    <input type="number" step="0.01" id="nb_galones" placeholder="0.00">
                                </div>
                                <div class="form-group">
                                    <label>Precio unitario</label>
                                    <input type="number" step="0.01" id="nb_precio" placeholder="0.00">
                                </div>
                                <div class="form-group">
                                    <label>Fecha</label>
                                    <input type="date" id="nb_fecha" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:12px">
                                <button type="button" class="btn" onclick="toggleBoletaForm('nueva')">Cancelar</button>
                                <button type="button" class="btn btn-primary" onclick="saveNewBoleta()">Guardar boleta</button>
                            </div>
                        </div>

                        {{-- Formulario inline: Boleta existente --}}
                        <div id="form-boleta-existente"
                             style="display:none;margin-top:12px;background:var(--surface,#fff);border:0.5px solid var(--border);border-radius:var(--radius,8px);padding:12px">
                            <div style="font-size:11px;font-weight:500;color:var(--text3);text-transform:uppercase;letter-spacing:.04em;margin-bottom:10px">
                                Asociar boleta existente
                            </div>
                            <div class="form-group">
                                <label>Número de boleta</label>
                                <input type="text" id="ex_numero" placeholder="Buscar por número…"
                                       list="boletas-existentes-list" autocomplete="off">
                                {{-- Puedes poblar este datalist desde el controlador --}}
                                <datalist id="boletas-existentes-list">
                                    @foreach($boletasDisponibles ?? [] as $b)
                                        <option value="{{ $b->numero_boleta }}">{{ $b->proveedor }} — {{ $b->galones }} gal</option>
                                    @endforeach
                                </datalist>
                            </div>
                            <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:12px">
                                <button type="button" class="btn" onclick="toggleBoletaForm('existente')">Cancelar</button>
                                <button type="button" class="btn btn-primary" onclick="saveExistingBoleta()">Asociar</button>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Inputs ocultos para el controller --}}
                <div id="hidden-new-boletas"></div>
                <div id="hidden-boletas-existing"></div>
                <script>
                // ── Boletas ──────────────────────────────────────────────
                let boletasNuevas = [];
                let boletasExistentes = [];

                function toggleBoletaForm(tipo) {
                    const ids = ['nueva', 'existente'];
                    ids.forEach(t => {
                        const el = document.getElementById('form-boleta-' + t);
                        if (t === tipo) {
                            el.style.display = el.style.display === 'none' ? 'block' : 'none';
                        } else {
                            el.style.display = 'none';
                        }
                    });
                }

                function renderBoletas() {
                    const list = document.getElementById('boletas-list');
                    const empty = document.getElementById('boletas-empty');
                    const total = boletasNuevas.length + boletasExistentes.length;

                    document.getElementById('boletas-count').textContent = total;

                    // Limpiar items previos (no el empty)
                    list.querySelectorAll('.boleta-item').forEach(el => el.remove());

                    if (total === 0) {
                        empty.style.display = 'block';
                        return;
                    }
                    empty.style.display = 'none';

                    boletasNuevas.forEach((b, i) => {
                        const meta = [b.proveedor, b.galones ? b.galones + ' gal' : '', b.precio ? 'S/ ' + b.precio + '/gal' : '']
                            .filter(Boolean).join(' · ');
                        list.insertAdjacentHTML('beforeend', `
                            <div class="boleta-item" style="display:flex;align-items:center;gap:10px;padding:8px 10px;
                                 background:var(--surface2,#f8f8f6);border-radius:var(--radius,8px);
                                 border:0.5px solid var(--border)">
                                <i class="ti ti-receipt" style="font-size:16px;color:var(--text2);flex-shrink:0"></i>
                                <div style="flex:1;min-width:0">
                                    <div style="font-size:13px;font-weight:500">${b.numero}</div>
                                    <div style="font-size:11px;color:var(--text3)">${meta}</div>
                                </div>
                                <button type="button" onclick="removeNueva(${i})"
                                        style="border:none;background:none;cursor:pointer;padding:2px 6px;
                                               border-radius:var(--radius,8px);color:var(--text3)"
                                        onmouseover="this.style.background='var(--danger-bg,#fcebeb)';this.style.color='var(--danger,#a32d2d)'"
                                        onmouseout="this.style.background='none';this.style.color='var(--text3)'>
                                    <i class="ti ti-x" style="font-size:14px"></i>
                                </button>
                            </div>`);
                    });

                    boletasExistentes.forEach((b, i) => {
                        list.insertAdjacentHTML('beforeend', `
                            <div class="boleta-item" style="display:flex;align-items:center;gap:10px;padding:8px 10px;
                                 background:var(--surface2,#f8f8f6);border-radius:var(--radius,8px);
                                 border:0.5px solid var(--border)">
                                <i class="ti ti-link" style="font-size:16px;color:var(--text2);flex-shrink:0"></i>
                                <div style="flex:1;min-width:0">
                                    <div style="font-size:13px;font-weight:500">${b.numero}</div>
                                    <div style="font-size:11px;color:var(--text3)">Boleta existente</div>
                                </div>
                                <button type="button" onclick="removeExistente(${i})"
                                        style="border:none;background:none;cursor:pointer;padding:2px 6px;
                                               border-radius:var(--radius,8px);color:var(--text3)"
                                        onmouseover="this.style.background='var(--danger-bg,#fcebeb)';this.style.color='var(--danger,#a32d2d)'"
                                        onmouseout="this.style.background='none';this.style.color='var(--text3)'>
                                    <i class="ti ti-x" style="font-size:14px"></i>
                                </button>
                            </div>`);
                    });

                    syncHiddenInputs();
                }

                function saveNewBoleta() {
                    const numero = document.getElementById('nb_numero').value.trim();
                    if (!numero) { document.getElementById('nb_numero').focus(); return; }

                    boletasNuevas.push({
                        numero,
                        proveedor: document.getElementById('nb_proveedor').value,
                        galones:   document.getElementById('nb_galones').value,
                        precio:    document.getElementById('nb_precio').value,
                        fecha:     document.getElementById('nb_fecha').value,
                    });

                    ['nb_numero','nb_proveedor','nb_galones','nb_precio'].forEach(id => {
                        document.getElementById(id).value = '';
                    });
                    document.getElementById('nb_fecha').value = '{{ date("Y-m-d") }}';

                    toggleBoletaForm('nueva');
                    renderBoletas();
                }

                function saveExistingBoleta() {
                    const numero = document.getElementById('ex_numero').value.trim();
                    if (!numero) { document.getElementById('ex_numero').focus(); return; }

                    boletasExistentes.push({ numero });
                    document.getElementById('ex_numero').value = '';

                    toggleBoletaForm('existente');
                    renderBoletas();
                }

                function removeNueva(i)     { boletasNuevas.splice(i, 1);     renderBoletas(); }
                function removeExistente(i) { boletasExistentes.splice(i, 1); renderBoletas(); }

                function syncHiddenInputs() {
                    const containerN = document.getElementById('hidden-new-boletas');
                    const containerE = document.getElementById('hidden-boletas-existing');
                    containerN.innerHTML = '';
                    containerE.innerHTML = '';

                    boletasNuevas.forEach((b, i) => {
                        ['numero','proveedor','galones','precio','fecha'].forEach(campo => {
                            const inp = document.createElement('input');
                            inp.type  = 'hidden';
                            inp.name  = `new_boletas[${i}][${campo}]`;
                            inp.value = b[campo] ?? '';
                            containerN.appendChild(inp);
                        });
                    });

                    boletasExistentes.forEach((b, i) => {
                        const inp = document.createElement('input');
                        inp.type  = 'hidden';
                        inp.name  = `existing_boletas[${i}]`;
                        inp.value = b.numero;
                        containerE.appendChild(inp);
                    });
                }
                </script>
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
    // (Funciones antiguas de filas de boletas removidas; se usa flujo modal nuevo)
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
