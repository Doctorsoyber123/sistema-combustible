@php
    $isEditing = old('_edit_id') == $b->id;
    $vehSel   = $isEditing ? old('vehiculo_id', $b->vehiculo_id) : $b->vehiculo_id;
    $fechaSel = $isEditing ? old('fecha', $b->fecha->format('Y-m-d')) : $b->fecha->format('Y-m-d');
@endphp
<div class="modal-overlay" id="modal-edit-boleta-{{ $b->id }}">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-title"><i class="ti ti-edit"></i> Editar boleta &mdash; {{ $b->numero_boleta }}</div>
            <button type="button" class="modal-close" onclick="closeModal('modal-edit-boleta-{{ $b->id }}')"><i class="ti ti-x"></i></button>
        </div>
        <form action="{{ route('boletas.update', $b) }}" method="POST" enctype="multipart/form-data"
              data-confirm="¿Guardar los cambios de esta boleta?">
            @csrf
            @method('PUT')
            <input type="hidden" name="_edit_id" value="{{ $b->id }}">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label>N&deg; de Boleta</label>
                        <input type="text" name="numero_boleta" value="{{ $isEditing ? old('numero_boleta', $b->numero_boleta) : $b->numero_boleta }}" required>
                        @if($isEditing) @error('numero_boleta') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
                    <div class="form-group">
                        <label>Fecha</label>
                        <input type="date" name="fecha" value="{{ $fechaSel }}" required>
                        @if($isEditing) @error('fecha') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
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
                        <label>Proveedor / Grifo</label>
                        <input type="text" name="proveedor" value="{{ $isEditing ? old('proveedor', $b->proveedor) : $b->proveedor }}">
                    </div>
                    <div class="form-group">
                        <label>Galones</label>
                        <input type="number" id="edit-b-galones-{{ $b->id }}" name="galones" step="0.01" min="0.01"
                               value="{{ $isEditing ? old('galones', $b->galones) : $b->galones }}" required
                               oninput="calcEditTotal({{ $b->id }})">
                        @if($isEditing) @error('galones') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
                    <div class="form-group">
                        <label>Precio por galon (S/)</label>
                        <input type="number" id="edit-b-precio-{{ $b->id }}" name="precio_galon" step="0.01" min="0.01"
                               value="{{ $isEditing ? old('precio_galon', $b->precio_galon) : $b->precio_galon }}" required
                               oninput="calcEditTotal({{ $b->id }})">
                        @if($isEditing) @error('precio_galon') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
                    <div class="form-group full">
                        <label>Total (S/) &mdash; calculado automaticamente</label>
                        <input type="text" id="edit-b-total-{{ $b->id }}" readonly
                               value="{{ number_format((float) ($isEditing ? old('galones', $b->galones) : $b->galones) * (float) ($isEditing ? old('precio_galon', $b->precio_galon) : $b->precio_galon), 2, '.', '') }}">
                    </div>
                    <div class="form-group full">
                        <label><i class="ti ti-paperclip"></i> Evidencia actual</label>
                        @if($b->evidencia)
                            @php $esPdf = \Illuminate\Support\Str::endsWith(strtolower($b->evidencia), '.pdf'); @endphp
                            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                                <a href="{{ asset('storage/' . $b->evidencia) }}" target="_blank" rel="noopener" class="chip chip-blue" style="text-decoration:none">
                                    <i class="ti ti-{{ $esPdf ? 'file-type-pdf' : 'photo' }}"></i> Ver archivo actual
                                </a>
                                <label style="display:inline-flex;align-items:center;gap:6px;font-size:12px;color:var(--text2);font-weight:400">
                                    <input type="checkbox" name="eliminar_evidencia" value="1" style="width:14px;height:14px;accent-color:var(--red)">
                                    Eliminar el archivo
                                </label>
                            </div>
                        @else
                            <span style="font-size:12px;color:var(--text3)">No tiene evidencia adjunta</span>
                        @endif
                    </div>
                    <div class="form-group full">
                        <label>Reemplazar / agregar evidencia (opcional)</label>
                        <input type="file" name="evidencia" accept=".jpg,.jpeg,.png,.webp,.pdf,image/*,application/pdf">
                        <span style="font-size:11px;color:var(--text3)">JPG, PNG, WEBP o PDF &middot; maximo 5 MB. Deja vacio para conservar el actual.</span>
                        @if($isEditing) @error('evidencia') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeModal('modal-edit-boleta-{{ $b->id }}')">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Guardar cambios</button>
            </div>
        </form>
    </div>
</div>
