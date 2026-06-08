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
                                <option value="{{ $t->id }}" data-descripcion="{{ e($t->descripcion) }}" @selected($tramoSel == $t->id)>{{ $t->nombre }}</option>
                            @endforeach
                        </select>
                        @if($isEditing) @error('tramo_id') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
                    <div class="form-group">
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
                <h4>Boleta (opcional)</h4>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Número de boleta</label>
                        <input type="text" name="boleta_numero" value="{{ $isEditing ? old('boleta_numero', $c->boleta->numero_boleta ?? '') : ($c->boleta->numero_boleta ?? '') }}" placeholder="Ej: B-00230">
                    </div>
                    <div class="form-group">
                        <label>Proveedor</label>
                        <input type="text" name="boleta_proveedor" value="{{ $isEditing ? old('boleta_proveedor', $c->boleta->proveedor ?? '') : ($c->boleta->proveedor ?? '') }}" placeholder="Proveedor">
                    </div>
                    <div class="form-group">
                        <label>Galones (boleta)</label>
                        <input type="number" name="boleta_galones" step="0.01" min="0" value="{{ $isEditing ? old('boleta_galones', $c->boleta->galones ?? '') : ($c->boleta->galones ?? '') }}" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label>Precio por galón</label>
                        <input type="number" name="boleta_precio" step="0.01" min="0" value="{{ $isEditing ? old('boleta_precio', $c->boleta->precio_galon ?? '') : ($c->boleta->precio_galon ?? '') }}" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label>Fecha (boleta)</label>
                        <input type="date" name="boleta_fecha" value="{{ $isEditing ? old('boleta_fecha', optional($c->boleta)->fecha?->format('Y-m-d')) : optional($c->boleta)->fecha?->format('Y-m-d') }}">
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
