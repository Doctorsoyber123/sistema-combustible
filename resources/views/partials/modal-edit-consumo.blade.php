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
                                <option value="{{ $t->id }}" data-descripcion="{{ e($t->descripcion) }}" data-turno="{{ e($t->turno) }}" @selected($tramoSel == $t->id)>{{ $t->nombre }}</option>
                            @endforeach
                        </select>
                        @if($isEditing) @error('tramo_id') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
                    <div class="form-group">
                        <small id="tramo-turno-edit-{{ $c->id }}" class="text-muted" style="display:block;font-weight:600">{{ $isEditing ? old('tramo_turno', $c->tramo->turno ?? '') : ($c->tramo->turno ?? '') }}</small>
                        <small id="tramo-desc-edit-{{ $c->id }}" class="text-muted">{{ $isEditing ? old('tramo_descripcion', $c->tramo->descripcion ?? '') : ($c->tramo->descripcion ?? '') }}</small>
                    </div>
                    <div class="form-group">
                        <label>Galones usados</label>
                        <input type="number" name="galones" step="0.01" min="0.01" value="{{ $isEditing ? old('galones', $c->galones) : $c->galones }}" required>
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
                <h4>Boletas (opcional)</h4>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Seleccionar boletas existentes</label>
                        <select name="boletas_existing[]" multiple style="min-height:90px">
                            @foreach($boletas as $b)
                                <option value="{{ $b->id }}" data-vehiculo-id="{{ $b->vehiculo_id }}" data-placa="{{ optional($b->vehiculo)->placa }}" @selected(optional($c->boletas)->contains($b->id))>{{ $b->numero_boleta }} — {{ $b->proveedor }} — {{ optional($b->fecha)->format('d/m/Y') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="min-width:100%">
                        <label>Nueva(s) boleta(s) (opcional)</label>
                        <div id="new-boletas-{{ $c->id }}">
                            @php $i = 0; @endphp
                            @foreach(old('new_boletas', $c->boletas->map(function($b){
                                return ['numero'=>$b->numero_boleta,'proveedor'=>$b->proveedor,'galones'=>$b->galones,'precio'=>$b->precio_galon,'fecha'=>optional($b->fecha)->format('Y-m-d')];
                            })->toArray()) as $nb)
                                <div class="boleta-row" data-index="{{ $i }}">
                                    <input type="text" name="new_boletas[{{ $i }}][numero]" value="{{ $nb['numero'] ?? '' }}" placeholder="Número" style="width:22%">
                                    <input type="text" name="new_boletas[{{ $i }}][proveedor]" value="{{ $nb['proveedor'] ?? '' }}" placeholder="Proveedor" style="width:28%">
                                    <input type="number" step="0.01" min="0" name="new_boletas[{{ $i }}][galones]" value="{{ $nb['galones'] ?? '' }}" placeholder="Galones" style="width:12%">
                                    <input type="number" step="0.01" min="0" name="new_boletas[{{ $i }}][precio]" value="{{ $nb['precio'] ?? '' }}" placeholder="Precio" style="width:12%">
                                    <input type="date" name="new_boletas[{{ $i }}][fecha]" value="{{ $nb['fecha'] ?? '' }}" style="width:14%">
                                    <button type="button" class="btn btn-sm" onclick="removeBoletaRow(this)">Eliminar</button>
                                </div>
                                @php $i++; @endphp
                            @endforeach
                        </div>
                        <div style="margin-top:8px">
                            <button type="button" class="btn" onclick="addBoletaRow({{ $c->id }})">+ Agregar nueva boleta</button>
                            <button type="button" class="btn" onclick="splitIntoBoletas({{ $c->id }})">Dividir galones en boletas</button>
                            <small class="text-muted" id="boletas-sum-{{ $c->id }}">Suma boletas: 0</small>
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
