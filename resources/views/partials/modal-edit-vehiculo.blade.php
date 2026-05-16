@php $isEditing = old('_edit_id') == $v->id; @endphp
<div class="modal-overlay" id="modal-edit-vehiculo-{{ $v->id }}">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-title"><i class="ti ti-edit"></i> Editar vehiculo &mdash; {{ $v->codigo }}</div>
            <button type="button" class="modal-close" onclick="closeModal('modal-edit-vehiculo-{{ $v->id }}')"><i class="ti ti-x"></i></button>
        </div>
        <form action="{{ route('vehiculos.update', $v) }}" method="POST" data-confirm="¿Guardar los cambios de este vehiculo?">
            @csrf
            @method('PUT')
            <input type="hidden" name="_edit_id" value="{{ $v->id }}">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Codigo</label>
                        <input type="text" name="codigo" value="{{ $isEditing ? old('codigo', $v->codigo) : $v->codigo }}" required>
                        @if($isEditing) @error('codigo') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
                    <div class="form-group">
                        <label>Tipo de vehiculo</label>
                        @php $tipoSel = $isEditing ? old('tipo', $v->tipo) : $v->tipo; @endphp
                        <select name="tipo" required>
                            @foreach($tiposVehiculo as $tv)
                                <option value="{{ $tv->nombre }}" @selected($tipoSel === $tv->nombre)>{{ $tv->nombre }}</option>
                            @endforeach
                            @if(! $tiposVehiculo->pluck('nombre')->contains($tipoSel))
                                <option value="{{ $tipoSel }}" selected>{{ $tipoSel }} (tipo en desuso)</option>
                            @endif
                        </select>
                        @if($isEditing) @error('tipo') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
                    <div class="form-group">
                        <label>Placa</label>
                        <input type="text" name="placa" value="{{ $isEditing ? old('placa', $v->placa) : $v->placa }}">
                        @if($isEditing) @error('placa') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
                    <div class="form-group">
                        <label>Modelo</label>
                        <input type="text" name="modelo" value="{{ $isEditing ? old('modelo', $v->modelo) : $v->modelo }}">
                        @if($isEditing) @error('modelo') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
                    <div class="form-group full">
                        <label>Estado</label>
                        @php $activoSel = $isEditing ? old('activo', $v->activo ? '1' : '0') : ($v->activo ? '1' : '0'); @endphp
                        <select name="activo">
                            <option value="1" @selected((string) $activoSel === '1')>Activo &mdash; disponible para registrar consumos y boletas</option>
                            <option value="0" @selected((string) $activoSel === '0')>Inactivo &mdash; oculto en los formularios</option>
                        </select>
                        <span style="font-size:11px;color:var(--text3)">
                            Los vehículos inactivos no aparecen como opción al registrar consumos o boletas, pero su historial se conserva.
                        </span>
                    </div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeModal('modal-edit-vehiculo-{{ $v->id }}')">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Guardar cambios</button>
            </div>
        </form>
    </div>
</div>
