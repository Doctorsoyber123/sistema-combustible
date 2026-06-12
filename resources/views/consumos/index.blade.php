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
                            <label>Vehículo</label>
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
                            <select name="tramo_id" id="sel-tramo-nuevo" required>
                                <option value="">Seleccionar...</option>
                                @foreach($tramos as $t)
                                    <option value="{{ $t->id }}"
                                            data-galones="{{ $t->galones ?? '' }}"
                                            @selected(old('tramo_id') == $t->id)>
                                        {{ $t->nombre }} ({{ rtrim(rtrim(number_format($t->km, 2), '0'), '.') }} km)
                                    </option>
                                @endforeach
                            </select>
                            @error('tramo_id') <span class="field-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label style="display:flex;align-items:center;gap:6px">
                                Galones usados
                                <span id="galones-hint-nuevo"
                                      style="font-size:10px;font-weight:500;padding:1px 7px;
                                             border-radius:20px;background:#fff4e6;color:#c06000;
                                             border:1px solid #f5c589">
                                    <i class="ti ti-arrows-transfer-down" style="font-size:10px"></i> del tramo
                                </span>
                            </label>
                            <input type="number" name="galones" id="inp-galones-nuevo"
                                   value="{{ old('galones') }}"
                                   step="0.01" min="0.01" placeholder="Selecciona un tramo…"
                                   readonly required
                                   style="background:var(--surface2,#f5f5f3);cursor:not-allowed;color:var(--text2)">
                            @error('galones') <span class="field-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Fecha</label>
                            <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required>
                            @error('fecha') <span class="field-error">{{ $message }}</span> @enderror
                        </div>

                        {{-- Operador como select --}}
                        <div class="form-group">
                            <label>Operador / Chofer</label>
                            <select name="operador">
                                <option value="">Sin asignar</option>
                                @foreach($trabajadores as $tr)
                                    <option value="{{ $tr->name }}" @selected(old('operador') == $tr->name)>
                                        {{ $tr->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('operador') <span class="field-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Observaciones</label>
                            <input type="text" name="observaciones"
                                   value="{{ old('observaciones') }}" placeholder="Opcional...">
                            @error('observaciones') <span class="field-error">{{ $message }}</span> @enderror
                        </div>

                    </div>
                </div>

                <hr>

                {{-- Toggle boletas --}}
                <label style="display:flex;align-items:center;gap:10px;padding:14px 20px;
                               cursor:pointer;user-select:none" for="chk-boletas"
                       onmouseover="this.style.background='var(--surface2)'"
                       onmouseout="this.style.background='none'">
                    <div style="position:relative;width:36px;height:20px;flex-shrink:0">
                        <input type="checkbox" id="chk-boletas" onchange="toggleBoletas(this)"
                               style="opacity:0;width:0;height:0;position:absolute">
                        <div id="switch-track"
                             style="position:absolute;inset:0;background:var(--border);
                                    border-radius:20px;transition:.2s"></div>
                        <div id="switch-thumb"
                             style="position:absolute;width:14px;height:14px;left:3px;top:3px;
                                    background:#fff;border-radius:50%;transition:.2s"></div>
                    </div>
                    <span style="font-size:13px;font-weight:500;display:flex;align-items:center;gap:6px">
                        <i class="ti ti-receipt" style="font-size:16px;color:var(--text2)"></i>
                        Asociar boletas
                        <span id="boletas-count"
                              style="display:none;font-size:11px;padding:2px 8px;border-radius:20px;
                                     background:#e6f1fb;color:#185fa5">0</span>
                    </span>
                    <span style="font-size:11px;color:var(--text3);margin-left:auto">Opcional</span>
                </label>

                {{-- Sección boletas (oculta por defecto) --}}
                <div id="seccion-boletas" style="display:none;padding:0 20px 20px">
                    <div style="background:var(--surface2,#f8f8f6);border:0.5px solid var(--border);
                                border-radius:var(--radius-lg,12px);padding:12px">

                        <div id="boletas-list" style="display:flex;flex-direction:column;gap:6px">
                            <div id="boletas-empty"
                                 style="text-align:center;padding:12px 0;color:var(--text3);font-size:12px">
                                <i class="ti ti-receipt-off" style="font-size:20px;display:block;margin-bottom:4px"></i>
                                Sin boletas aún
                            </div>
                        </div>

                        <div style="height:0.5px;background:var(--border);margin:10px 0"></div>

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
                             style="display:none;margin-top:10px;background:var(--surface,#fff);
                                    border:0.5px solid var(--border);border-radius:var(--radius,8px);padding:12px">
                            <div style="font-size:11px;font-weight:500;color:var(--text3);
                                        text-transform:uppercase;letter-spacing:.04em;margin-bottom:8px">
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
                                <div class="form-group" style="grid-column:1/-1">
                                    <label>Fecha</label>
                                    <input type="date" id="nb_fecha" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:10px">
                                <button type="button" class="btn" onclick="toggleBoletaForm('nueva')">Cancelar</button>
                                <button type="button" class="btn btn-primary" onclick="saveNewBoleta()">Guardar boleta</button>
                            </div>
                        </div>

                        {{-- Formulario inline: Boleta existente --}}
                        <div id="form-boleta-existente"
                             style="display:none;margin-top:10px;background:var(--surface,#fff);
                                    border:0.5px solid var(--border);border-radius:var(--radius,8px);padding:12px">
                            <div style="font-size:11px;font-weight:500;color:var(--text3);
                                        text-transform:uppercase;letter-spacing:.04em;margin-bottom:8px">
                                Asociar boleta existente
                            </div>

                            {{-- Buscador --}}
                            <div style="position:relative;margin-bottom:8px">
                                <i class="ti ti-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text3);font-size:14px;pointer-events:none"></i>
                                <input type="text" id="ex_search"
                                       placeholder="Buscar por número, proveedor…"
                                       autocomplete="off"
                                       oninput="filterBoletasExistentes()"
                                       style="width:100%;padding:7px 10px 7px 32px;box-sizing:border-box">
                            </div>

                            {{-- Lista scrollable --}}
                            <div id="ex_lista"
                                 style="max-height:180px;overflow-y:auto;display:flex;flex-direction:column;
                                        gap:4px;border:0.5px solid var(--border);border-radius:var(--radius,8px);
                                        padding:6px;background:var(--surface2,#f8f8f6)">
                                @forelse($boletasDisponibles ?? [] as $b)
                                    <div class="ex-boleta-row"
                                         data-numero="{{ $b->numero_boleta }}"
                                         data-search="{{ strtolower($b->numero_boleta . ' ' . $b->proveedor) }}"
                                         onclick="selectBoletaExistente(this)"
                                         style="display:flex;align-items:center;gap:10px;padding:7px 10px;
                                                border-radius:calc(var(--radius,8px) - 2px);
                                                cursor:pointer;background:var(--surface,#fff);
                                                border:0.5px solid transparent;transition:.15s">
                                        <i class="ti ti-receipt" style="font-size:15px;color:var(--text3);flex-shrink:0"></i>
                                        <div style="flex:1;min-width:0">
                                            <div style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                                {{ $b->numero_boleta }}
                                            </div>
                                            <div style="font-size:11px;color:var(--text3)">
                                                {{ $b->proveedor }} &middot; {{ number_format($b->galones, 2) }} gal
                                            </div>
                                        </div>
                                        <i class="ti ti-circle-check ex-check" style="font-size:17px;color:transparent"></i>
                                    </div>
                                @empty
                                    <div style="text-align:center;padding:16px 0;color:var(--text3);font-size:12px">
                                        <i class="ti ti-receipt-off" style="font-size:20px;display:block;margin-bottom:4px"></i>
                                        Sin boletas disponibles
                                    </div>
                                @endforelse
                            </div>

                            <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:10px">
                                <button type="button" class="btn" onclick="toggleBoletaForm('existente')">Cancelar</button>
                                <button type="button" class="btn btn-primary" onclick="saveExistingBoleta()">Asociar</button>
                            </div>
                        </div>

                    </div>
                </div>

                <div id="hidden-new-boletas"></div>
                <div id="hidden-boletas-existing"></div>

                <div class="modal-foot">
                    <button type="button" class="btn" onclick="closeModal('modal-consumo')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check"></i> Registrar consumo
                    </button>
                </div>

            </form>
        @endif
    </div>
</div>
<script>
// ── Switch visual ────────────────────────────────────────
function toggleBoletas(chk) {
    const seccion = document.getElementById('seccion-boletas');
    const track   = document.getElementById('switch-track');
    const thumb   = document.getElementById('switch-thumb');
    const badge   = document.getElementById('boletas-count');

    seccion.style.display = chk.checked ? 'block' : 'none';
    badge.style.display   = chk.checked ? 'inline' : 'none';
    track.style.background = chk.checked ? '#e85d04' : 'var(--border)';
    thumb.style.transform  = chk.checked ? 'translateX(16px)' : 'translateX(0)';

    if (!chk.checked) {
        boletasNuevas = [];
        boletasExistentes = [];
        renderBoletas();
        ['nueva', 'existente'].forEach(t => {
            document.getElementById('form-boleta-' + t).style.display = 'none';
        });
    }
}

// ── Boletas ──────────────────────────────────────────────
let boletasNuevas     = [];
let boletasExistentes = [];

function toggleBoletaForm(tipo) {
    ['nueva', 'existente'].forEach(t => {
        const el = document.getElementById('form-boleta-' + t);
        el.style.display = (t === tipo && el.style.display === 'none') ? 'block' : 'none';
    });
}

function renderBoletas() {
    const list  = document.getElementById('boletas-list');
    const empty = document.getElementById('boletas-empty');
    const badge = document.getElementById('boletas-count');
    const total = boletasNuevas.length + boletasExistentes.length;

    badge.textContent = total;
    list.querySelectorAll('.boleta-item').forEach(el => el.remove());
    empty.style.display = total ? 'none' : 'block';

    boletasNuevas.forEach((b, i) => {
        const meta = [b.proveedor, b.galones ? b.galones + ' gal' : ''].filter(Boolean).join(' · ');
        list.insertAdjacentHTML('beforeend', `
            <div class="boleta-item" style="display:flex;align-items:center;gap:8px;padding:7px 10px;
                 background:var(--surface,#fff);border-radius:var(--radius,8px);
                 border:0.5px solid var(--border)">
                <i class="ti ti-receipt" style="font-size:15px;color:var(--text2);flex-shrink:0"></i>
                <div style="flex:1">
                    <div style="font-size:13px;font-weight:500">${b.numero}</div>
                    <div style="font-size:11px;color:var(--text3)">${meta || '—'}</div>
                </div>
                <button type="button" onclick="removeNueva(${i})"
                        style="border:none;background:none;cursor:pointer;padding:2px 6px;
                               border-radius:var(--radius,8px);color:var(--text3);font-size:15px"
                        onmouseover="this.style.color='#a32d2d'"
                        onmouseout="this.style.color='var(--text3)'">
                    <i class="ti ti-x"></i>
                </button>
            </div>`);
    });

    boletasExistentes.forEach((b, i) => {
        list.insertAdjacentHTML('beforeend', `
            <div class="boleta-item" style="display:flex;align-items:center;gap:8px;padding:7px 10px;
                 background:var(--surface,#fff);border-radius:var(--radius,8px);
                 border:0.5px solid var(--border)">
                <i class="ti ti-link" style="font-size:15px;color:var(--text2);flex-shrink:0"></i>
                <div style="flex:1">
                    <div style="font-size:13px;font-weight:500">${b.numero}</div>
                    <div style="font-size:11px;color:var(--text3)">Boleta existente</div>
                </div>
                <button type="button" onclick="removeExistente(${i})"
                        style="border:none;background:none;cursor:pointer;padding:2px 6px;
                               border-radius:var(--radius,8px);color:var(--text3);font-size:15px"
                        onmouseover="this.style.color='#a32d2d'"
                        onmouseout="this.style.color='var(--text3)'">
                    <i class="ti ti-x"></i>
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
        proveedor : document.getElementById('nb_proveedor').value,
        galones   : document.getElementById('nb_galones').value,
        precio    : document.getElementById('nb_precio').value,
        fecha     : document.getElementById('nb_fecha').value,
    });

    ['nb_numero','nb_proveedor','nb_galones','nb_precio'].forEach(id => {
        document.getElementById(id).value = '';
    });

    toggleBoletaForm('__none__');
    renderBoletas();
}

function selectBoletaExistente(el) {
    // deselect all
    document.querySelectorAll('#ex_lista .ex-boleta-row').forEach(r => {
        r.style.background      = 'var(--surface,#fff)';
        r.style.borderColor     = 'transparent';
        r.querySelector('.ex-check').style.color = 'transparent';
    });
    // select clicked
    el.style.background  = '#e8f5e9';
    el.style.borderColor = '#4caf50';
    el.querySelector('.ex-check').style.color = '#388e3c';
}

function filterBoletasExistentes() {
    const q = document.getElementById('ex_search').value.toLowerCase().trim();
    document.querySelectorAll('#ex_lista .ex-boleta-row').forEach(r => {
        r.style.display = (!q || r.dataset.search.includes(q)) ? 'flex' : 'none';
    });
}

function saveExistingBoleta() {
    const selected = document.querySelector('#ex_lista .ex-boleta-row[style*="#e8f5e9"]');
    if (!selected) {
        // shake search box lightly if nothing selected
        const s = document.getElementById('ex_search');
        s.style.outline = '2px solid #e85d04';
        setTimeout(() => { s.style.outline = ''; }, 1000);
        return;
    }
    const numero = selected.dataset.numero;
    if (boletasExistentes.some(b => b.numero === numero)) {
        toggleBoletaForm('__none__');
        return; // already added
    }
    boletasExistentes.push({ numero });
    // reset search + deselect
    document.getElementById('ex_search').value = '';
    filterBoletasExistentes();
    document.querySelectorAll('#ex_lista .ex-boleta-row').forEach(r => {
        r.style.background  = 'var(--surface,#fff)';
        r.style.borderColor = 'transparent';
        r.querySelector('.ex-check').style.color = 'transparent';
    });
    toggleBoletaForm('__none__');
    renderBoletas();
}

function removeNueva(i)     { boletasNuevas.splice(i, 1);     renderBoletas(); }
function removeExistente(i) { boletasExistentes.splice(i, 1); renderBoletas(); }

function syncHiddenInputs() {
    const cN = document.getElementById('hidden-new-boletas');
    const cE = document.getElementById('hidden-boletas-existing');
    cN.innerHTML = '';
    cE.innerHTML = '';

    boletasNuevas.forEach((b, i) => {
        ['numero','proveedor','galones','precio','fecha'].forEach(campo => {
            const inp = document.createElement('input');
            inp.type  = 'hidden';
            inp.name  = `new_boletas[${i}][${campo}]`;
            inp.value = b[campo] ?? '';
            cN.appendChild(inp);
        });
    });

    boletasExistentes.forEach((b, i) => {
        const inp = document.createElement('input');
        inp.type  = 'hidden';
        inp.name  = `existing_boletas[${i}]`;
        inp.value = b.numero;
        cE.appendChild(inp);
    });
}
</script>

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

    // ── Auto-llenar galones desde el tramo (modal NUEVO) ──────────────
    const selTramoNuevo  = document.getElementById('sel-tramo-nuevo');
    const inpGalonesNuevo = document.getElementById('inp-galones-nuevo');
    const galonesHintNuevo = document.getElementById('galones-hint-nuevo');

    if (selTramoNuevo && inpGalonesNuevo) {
        selTramoNuevo.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            const gal = opt ? opt.dataset.galones : '';
            if (gal && parseFloat(gal) > 0) {
                inpGalonesNuevo.value = parseFloat(gal).toFixed(2);
            } else {
                inpGalonesNuevo.value = '';
            }
        });
    }

    // ── Auto-llenar galones desde el tramo (modales EDICIÓN) ──────────
    document.querySelectorAll('.modal-overlay[id^="modal-edit-consumo-"]').forEach(function(modal) {
        const selTramo  = modal.querySelector('select[name="tramo_id"]');
        const inpGalones = modal.querySelector('input[name="galones"]');
        if (!selTramo || !inpGalones) return;
        selTramo.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            const gal = opt ? opt.dataset.galones : '';
            inpGalones.value = (gal && parseFloat(gal) > 0) ? parseFloat(gal).toFixed(2) : '';
        });
    });

    // ── Filtrar boletas por vehiculo (modal principal y edición) ──────
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
    const mVeh = document.querySelector('#modal-consumo select[name="vehiculo_id"]');
    const mBoletas = document.querySelector('#modal-consumo select[name="boletas_existing[]"]');
    if(mVeh && mBoletas){ mVeh.addEventListener('change', ()=>applyFilterForSel(mVeh, mBoletas)); applyFilterForSel(mVeh, mBoletas); }
    document.querySelectorAll('.modal-overlay').forEach(function(modal){
        const vs = modal.querySelector('select[name="vehiculo_id"]');
        const bs = modal.querySelector('select[name="boletas_existing[]"]');
        if(vs && bs){ vs.addEventListener('change', ()=>applyFilterForSel(vs, bs)); applyFilterForSel(vs, bs); }
    });

    // ── AJAX pagination ───────────────────────────────────────────────
    document.addEventListener('click', function(e){
        const a = e.target.closest('.pagination a.page-link');
        if(!a) return;
        if(!document.getElementById('consumos-list')) return;
        e.preventDefault();
        const url = a.href;
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
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
