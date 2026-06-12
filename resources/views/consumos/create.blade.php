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
                <h4>Boletas asociadas <small id="boletas-count" class="text-muted">(0)</small></h4>
                <div class="card" style="padding:12px; margin-bottom:12px">
                    <div id="boletas-list" style="display:flex;flex-direction:column;gap:8px">
                        {{-- visible list of compact boleta cards will be appended here --}}
                    </div>
                    <div style="margin-top:12px;display:flex;gap:8px">
                        <button type="button" class="btn" onclick="openNewBoletaModal()">+ Crear nueva boleta</button>
                        <button type="button" class="btn" onclick="openAssociateBoletaModal()">+ Asociar boleta existente</button>
                    </div>
                </div>

                {{-- hidden inputs containers so controller receives expected arrays --}}
                <div id="hidden-new-boletas"></div>
                <div id="hidden-boletas-existing"></div>

                <!-- Modal: Crear nueva boleta (cliente-side, se añade al enviar consumo) -->
                <div class="modal-overlay" id="modal-new-boleta" aria-hidden="true">
                    <div class="modal-box modal-sm">
                        <div class="modal-head">
                            <div class="modal-title">Nueva boleta</div>
                            <button type="button" class="modal-close" onclick="closeNewBoletaModal()">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>Número</label>
                                    <input type="text" id="nb_numero">
                                </div>
                                <div class="form-group">
                                    <label>Proveedor</label>
                                    <input type="text" id="nb_proveedor">
                                </div>
                                <div class="form-group">
                                    <label>Galones</label>
                                    <input type="number" step="0.01" id="nb_galones">
                                </div>
                                <div class="form-group">
                                    <label>Precio</label>
                                    <input type="number" step="0.01" id="nb_precio">
                                </div>
                                <div class="form-group">
                                    <label>Fecha</label>
                                    <input type="date" id="nb_fecha" value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="form-group">
                                    <label>Evidencia (imagen/PDF)</label>
                                    <input type="file" id="nb_evidencia" accept="image/*,.pdf">
                                </div>
                            </div>
                        </div>
                        <div class="modal-foot">
                            <button type="button" class="btn" onclick="closeNewBoletaModal()">Cancelar</button>
                            <button type="button" class="btn btn-primary" onclick="saveNewBoleta()">Guardar</button>
                        </div>
                    </div>
                </div>

                <!-- Modal: Asociar boleta existente -->
                <div class="modal-overlay" id="modal-associate-boleta" aria-hidden="true">
                    <div class="modal-box modal-sm">
                        <div class="modal-head">
                            <div class="modal-title">Asociar boleta existente</div>
                            <button type="button" class="modal-close" onclick="closeAssociateBoletaModal()">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="search" id="assoc_search" placeholder="Buscar número, proveedor..." oninput="filterExistingBoletas()">
                            </div>
                            <div style="max-height:260px;overflow:auto;border-top:1px solid var(--border);padding-top:8px" id="assoc_list">
                                @foreach($boletas as $b)
                                    <label style="display:block;padding:6px 4px;border-radius:6px;cursor:pointer">
                                        <input type="checkbox" class="assoc-checkbox" data-id="{{ $b->id }}"> {{ $b->numero_boleta }} - {{ $b->proveedor }} - {{ optional($b->fecha)->format('d/m/Y') }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-foot">
                            <button type="button" class="btn" onclick="closeAssociateBoletaModal()">Cancelar</button>
                            <button type="button" class="btn btn-primary" onclick="saveAssociateSelection()">Asociar</button>
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
    // tramo description/turno update
    const select = document.querySelector('select[name="tramo_id"]');
    const desc = document.getElementById('tramo-desc');
    function update(){
        const opt = select.options[select.selectedIndex];
        desc.textContent = opt ? (opt.dataset.descripcion || '') : '';
        const turnoEl = document.getElementById('tramo-turno');
        if(turnoEl) turnoEl.textContent = opt ? (opt.dataset.turno || '') : '';
    }
    if(select){ select.addEventListener('change', update); update(); }

    // Boletas client-side handling for create form
    let newIdx = 0;
    function updateBoletasCount(){
        const list = document.getElementById('boletas-list');
        const count = list ? list.children.length : 0;
        const el = document.getElementById('boletas-count'); if(el) el.textContent = `(${count})`;
    }
    window.openNewBoletaModal = function(){ document.getElementById('modal-new-boleta').classList.add('open'); }
    window.closeNewBoletaModal = function(){ document.getElementById('modal-new-boleta').classList.remove('open'); }
    window.openAssociateBoletaModal = function(){ document.getElementById('modal-associate-boleta').classList.add('open'); }
    window.closeAssociateBoletaModal = function(){ document.getElementById('modal-associate-boleta').classList.remove('open'); }

    window.saveNewBoleta = function(){
        const numero = document.getElementById('nb_numero').value.trim();
        const proveedor = document.getElementById('nb_proveedor').value.trim();
        const galones = document.getElementById('nb_galones').value;
        const precio = document.getElementById('nb_precio').value;
        const fecha = document.getElementById('nb_fecha').value;
        const evidencia = document.getElementById('nb_evidencia').files[0];
        if(!numero && !proveedor && !galones) return alert('Completa al menos número, proveedor o galones');
        const vehSel = document.querySelector('select[name="vehiculo_id"]');
        const vehId = vehSel ? vehSel.value : null;
        if(!vehId) return alert('Selecciona un vehículo antes de crear la boleta');

        const fd = new FormData();
        fd.append('numero_boleta', numero);
        fd.append('proveedor', proveedor);
        fd.append('galones', galones);
        fd.append('precio_galon', precio);
        fd.append('fecha', fecha);
        fd.append('vehiculo_id', vehId);
        if(evidencia) fd.append('evidencia', evidencia);

        const url = '{{ route('boletas.store') }}';
        fetch(url, { method: 'POST', body: fd, headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
            .then(r => r.json())
            .then(json => {
                if(json.id){
                    // add hidden existing boleta input
                    const hidden = document.getElementById('hidden-boletas-existing');
                    const inp = document.createElement('input'); inp.type='hidden'; inp.name='boletas_existing[]'; inp.value = json.id; hidden.appendChild(inp);
                    // add card
                    const list = document.getElementById('boletas-list');
                    const card = document.createElement('div'); card.className='card'; card.style.padding='8px';
                    card.innerHTML = `<strong>${json.numero_boleta} - ${json.proveedor || '-'}</strong><div style="font-size:12px;color:var(--text3)">${parseFloat(json.galones||0).toFixed(2)} gal &middot; S/ ${ (json.total||0).toFixed(2) }</div>`;
                    list.appendChild(card);
                    closeNewBoletaModal(); updateBoletasCount();
                } else {
                    alert('Error al crear boleta');
                }
            }).catch(err=>{ console.error(err); alert('Error al crear boleta'); });
    }

    window.filterExistingBoletas = function(){
        const q = document.getElementById('assoc_search').value.toLowerCase();
        document.querySelectorAll('#assoc_list label').forEach(lbl=>{
            lbl.style.display = lbl.textContent.toLowerCase().includes(q) ? 'block' : 'none';
        });
    }

    window.saveAssociateSelection = function(){
        const checked = Array.from(document.querySelectorAll('#assoc_list .assoc-checkbox:checked'));
        if(checked.length === 0) return closeAssociateBoletaModal();
        const hidden = document.getElementById('hidden-boletas-existing');
        const list = document.getElementById('boletas-list');
        checked.forEach(cb=>{
            const id = cb.dataset.id;
            // avoid duplicates
            if(hidden.querySelector(`input[value="${id}"]`)) return;
            // append hidden input
            const inp = document.createElement('input'); inp.type='hidden'; inp.name='boletas_existing[]'; inp.value = id; hidden.appendChild(inp);
            // append visible card (label text)
            const label = cb.closest('label').textContent.trim();
            const card = document.createElement('div'); card.className='card'; card.style.padding='8px'; card.textContent = label;
            list.appendChild(card);
        });
        closeAssociateBoletaModal(); updateBoletasCount();
    }

    // close modals when clicking outside
    document.querySelectorAll('.modal-overlay').forEach(m=>{
        m.addEventListener('click', function(e){ if(e.target === m) m.classList.remove('open'); });
    });

    updateBoletasCount();
});
</script>
@endsection
