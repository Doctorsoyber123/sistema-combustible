@php
    $isEditing = old('_edit_id') == $c->id;
    $vehSel    = $isEditing ? old('vehiculo_id', $c->vehiculo_id) : $c->vehiculo_id;
    $tramoSel  = $isEditing ? old('tramo_id', $c->tramo_id) : $c->tramo_id;
    $fechaSel  = $isEditing ? old('fecha', $c->fecha->format('Y-m-d')) : $c->fecha->format('Y-m-d');
@endphp
<div class="modal-overlay" id="modal-edit-consumo-{{ $c->id }}">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-title"><i class="ti ti-edit"></i> Editar consumo &mdash; {{ $c->fecha->format('d/m/Y') }} / {{ $c->vehiculo->codigo ?? '-' }}</div>
            <button type="button" class="modal-close" onclick="closeModal('modal-edit-consumo-{{ $c->id }}')"><i class="ti ti-x"></i></button>
        </div>
        <form action="{{ route('consumos.update', $c) }}" method="POST" data-confirm="¿Guardar los cambios de este consumo?">
            @csrf
            @method('PUT')
            <input type="hidden" name="_edit_id" value="{{ $c->id }}">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Vehiculo</label>
                        <select name="vehiculo_id" required>
                            @foreach($vehiculos as $v)
                                <option value="{{ $v->id }}" @selected($vehSel == $v->id)>{{ $v->codigo }} &mdash; {{ $v->tipo }}</option>
                            @endforeach
                        </select>
                        @if($isEditing) @error('vehiculo_id') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
                    <div class="form-group">
                        <label>Tramo</label>
                        <select name="tramo_id" required>
                             @foreach($tramos as $t)
                                 <option value="{{ $t->id }}"
                                         data-galones="{{ $t->galones ?? '' }}"
                                         @selected($tramoSel == $t->id)>{{ $t->nombre }}</option>
                             @endforeach
                        </select>
                        @if($isEditing) @error('tramo_id') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:6px">
                            Galones usados
                            <span style="font-size:10px;font-weight:500;padding:1px 7px;
                                         border-radius:20px;background:#fff4e6;color:#c06000;
                                         border:1px solid #f5c589">
                                <i class="ti ti-arrows-transfer-down" style="font-size:10px"></i> del tramo
                            </span>
                        </label>
                        <input type="number" name="galones" step="0.01" min="0.01"
                               value="{{ $isEditing ? old('galones', $c->galones) : $c->galones }}"
                               readonly required
                               style="background:var(--surface2,#f5f5f3);cursor:not-allowed;color:var(--text2)">
                        @if($isEditing) @error('galones') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
                    <div class="form-group">
                        <label>Fecha</label>
                        <input type="date" name="fecha" value="{{ $fechaSel }}" required>
                        @if($isEditing) @error('fecha') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
                    <div class="form-group">
                        <label>Operador / Chofer</label>
                        <input type="text" name="operador" list="trabajadores-list" autocomplete="off"
                               value="{{ $isEditing ? old('operador', $c->operador) : $c->operador }}"
                               placeholder="Escribe para buscar...">
                        @if($isEditing) @error('operador') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
                    <div class="form-group">
                        <label>Observaciones</label>
                        <input type="text" name="observaciones" value="{{ $isEditing ? old('observaciones', $c->observaciones) : $c->observaciones }}">
                        @if($isEditing) @error('observaciones') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
                </div>
                <hr>
                <h4>Boletas asociadas <small id="boletas-count-{{ $c->id }}" class="text-muted">({{ $c->boletas->count() }})</small></h4>
                <div class="card" style="padding:12px; margin-bottom:12px">
                    <div id="boletas-list-{{ $c->id }}" style="display:flex;flex-direction:column;gap:8px">
                        @foreach($c->boletas as $b)
                            <div class="card" style="padding:8px">
                                <strong>{{ $b->numero_boleta }} - {{ $b->proveedor }}</strong>
                                <div style="font-size:12px;color:var(--text3)">{{ number_format($b->galones,2) }} gal &middot; S/ {{ number_format($b->total ?? ($b->galones*$b->precio_galon ?? 0),2) }}</div>
                            </div>
                        @endforeach
                    </div>
                    <div style="margin-top:12px;display:flex;gap:8px">
                        <button type="button" class="btn" onclick="openNewBoletaModal({{ $c->id }})">+ Crear nueva boleta</button>
                        <button type="button" class="btn" onclick="openAssociateBoletaModal({{ $c->id }})">+ Asociar boleta existente</button>
                    </div>
                </div>

                <div id="hidden-new-boletas-{{ $c->id }}"></div>
                <div id="hidden-boletas-existing-{{ $c->id }}"></div>
                <!-- Modals específicos para este consumo (crear/asociar boleta) -->
                <div class="modal-overlay" id="modal-new-boleta-{{ $c->id }}" aria-hidden="true">
                    <div class="modal-box modal-sm">
                        <div class="modal-head">
                            <div class="modal-title">Nueva boleta</div>
                            <button type="button" class="modal-close" onclick="closeNewBoletaModal({{ $c->id }})">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="form-grid">
                                <div class="form-group"><label>Número</label><input type="text" id="nb_numero_{{ $c->id }}"></div>
                                <div class="form-group"><label>Proveedor</label><input type="text" id="nb_proveedor_{{ $c->id }}"></div>
                                <div class="form-group"><label>Galones</label><input type="number" step="0.01" id="nb_galones_{{ $c->id }}"></div>
                                <div class="form-group"><label>Precio</label><input type="number" step="0.01" id="nb_precio_{{ $c->id }}"></div>
                                <div class="form-group"><label>Fecha</label><input type="date" id="nb_fecha_{{ $c->id }}" value="{{ date('Y-m-d') }}"></div>
                                    <div class="form-group"><label>Evidencia (imagen/PDF)</label><input type="file" id="nb_evidencia_{{ $c->id }}" accept="image/*,.pdf"></div>
                            </div>
                        </div>
                        <div class="modal-foot">
                            <button type="button" class="btn" onclick="closeNewBoletaModal({{ $c->id }})">Cancelar</button>
                            <button type="button" class="btn btn-primary" onclick="saveNewBoleta({{ $c->id }})">Guardar</button>
                        </div>
                    </div>
                </div>

                <div class="modal-overlay" id="modal-associate-boleta-{{ $c->id }}" aria-hidden="true">
                    <div class="modal-box modal-sm">
                        <div class="modal-head">
                            <div class="modal-title">Asociar boleta existente</div>
                            <button type="button" class="modal-close" onclick="closeAssociateBoletaModal({{ $c->id }})">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="search" id="assoc_search_{{ $c->id }}" placeholder="Buscar número, proveedor..." oninput="filterExistingBoletas({{ $c->id }})">
                            </div>
                            <div style="max-height:260px;overflow:auto;border-top:1px solid var(--border);padding-top:8px" id="assoc_list_{{ $c->id }}">
                                @foreach($boletas as $b)
                                    <label style="display:block;padding:6px 4px;border-radius:6px;cursor:pointer">
                                        <input type="checkbox" class="assoc-checkbox-{{ $c->id }}" data-id="{{ $b->id }}"> {{ $b->numero_boleta }} - {{ $b->proveedor }} - {{ optional($b->fecha)->format('d/m/Y') }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-foot">
                            <button type="button" class="btn" onclick="closeAssociateBoletaModal({{ $c->id }})">Cancelar</button>
                            <button type="button" class="btn btn-primary" onclick="saveAssociateSelection({{ $c->id }})">Asociar</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeModal('modal-edit-consumo-{{ $c->id }}')">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Guardar cambios</button>
            </div>
        </form>
    </div>
</div>
