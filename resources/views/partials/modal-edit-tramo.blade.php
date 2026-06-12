@php $isEditing = old('_edit_id') == $t->id; @endphp
<div class="modal-overlay" id="modal-edit-tramo-{{ $t->id }}">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-title"><i class="ti ti-edit"></i> Editar tramo &mdash; {{ $t->nombre }}</div>
            <button type="button" class="modal-close" onclick="closeModal('modal-edit-tramo-{{ $t->id }}')"><i class="ti ti-x"></i></button>
        </div>
        <form action="{{ route('tramos.update', $t) }}" method="POST" data-confirm="¿Guardar los cambios de este tramo?">
            @csrf
            @method('PUT')
            <input type="hidden" name="_edit_id" value="{{ $t->id }}">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nombre del tramo</label>
                        <input type="text" name="nombre" value="{{ $isEditing ? old('nombre', $t->nombre) : $t->nombre }}" required>
                        @if($isEditing) @error('nombre') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
                    <div class="form-group">
                        <label>Distancia (km)</label>
                        <input type="number" name="km" step="0.1" min="0.1" value="{{ $isEditing ? old('km', $t->km) : $t->km }}" required>
                        @if($isEditing) @error('km') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
                    <div class="form-group">
                        <label>Punto de origen</label>
                        <input type="text" name="origen" value="{{ $isEditing ? old('origen', $t->origen) : $t->origen }}" required>
                        @if($isEditing) @error('origen') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
                    <div class="form-group">
                        <label>Punto de destino</label>
                        <input type="text" name="destino" value="{{ $isEditing ? old('destino', $t->destino) : $t->destino }}" required>
                        @if($isEditing) @error('destino') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
                        <div class="form-group">
                            <label>Galones</label>
                            <input type="number" name="galones" step="0.01" min="0" value="{{ $isEditing ? old('galones', $t->galones) : $t->galones }}" placeholder="0.00" required>
                            @if($isEditing) @error('galones') <span class="field-error">{{ $message }}</span> @enderror @endif
                        </div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeModal('modal-edit-tramo-{{ $t->id }}')">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Guardar cambios</button>
            </div>
        </form>
    </div>
</div>
