@php
    $isEditing = old('_edit_id') == $u->id;
    $roleSel   = $isEditing ? old('role', $u->role) : $u->role;
    $esYo      = $u->id === auth()->id();
@endphp
<div class="modal-overlay" id="modal-edit-usuario-{{ $u->id }}">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-title"><i class="ti ti-edit"></i> Editar usuario &mdash; {{ $u->name }}</div>
            <button type="button" class="modal-close" onclick="closeModal('modal-edit-usuario-{{ $u->id }}')"><i class="ti ti-x"></i></button>
        </div>
        <form action="{{ route('usuarios.update', $u) }}" method="POST" data-confirm="¿Guardar los cambios de este usuario?">
            @csrf
            @method('PUT')
            <input type="hidden" name="_edit_id" value="{{ $u->id }}">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nombre completo</label>
                        <input type="text" name="name" value="{{ $isEditing ? old('name', $u->name) : $u->name }}" required>
                        @if($isEditing) @error('name') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
                    <div class="form-group">
                        <label>Usuario (para iniciar sesion)</label>
                        <input type="text" name="username" value="{{ $isEditing ? old('username', $u->username) : $u->username }}" required>
                        @if($isEditing) @error('username') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
                    <div class="form-group">
                        <label>Nueva contraseña <span style="color:var(--text3);font-weight:400">(opcional)</span></label>
                        <input type="password" name="password" placeholder="Dejar vacio para conservar la actual">
                        @if($isEditing) @error('password') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
                    <div class="form-group">
                        <label>Confirmar nueva contraseña</label>
                        <input type="password" name="password_confirmation" placeholder="Solo si pusiste una nueva">
                    </div>
                    <div class="form-group">
                        <label>Rol</label>
                        <select name="role" required {{ $esYo ? 'disabled' : '' }}>
                            <option value="trabajador" @selected($roleSel === 'trabajador')>Trabajador</option>
                            <option value="admin" @selected($roleSel === 'admin')>Administrador</option>
                        </select>
                        @if($esYo)
                            <input type="hidden" name="role" value="{{ $u->role }}">
                            <span style="font-size:11px;color:var(--text3)">No puedes cambiarte tu propio rol.</span>
                        @endif
                        @if($isEditing) @error('role') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        @php $activoSel = $isEditing ? old('activo', $u->activo ? '1' : '0') : ($u->activo ? '1' : '0'); @endphp
                        <select name="activo" {{ $esYo ? 'disabled' : '' }}>
                            <option value="1" @selected($activoSel === '1' || $activoSel === 1 || $activoSel === true)>Activo</option>
                            <option value="0" @selected($activoSel === '0' || $activoSel === 0 || $activoSel === false)>Inactivo</option>
                        </select>
                        @if($esYo)
                            <input type="hidden" name="activo" value="1">
                            <span style="font-size:11px;color:var(--text3)">No puedes desactivarte a ti mismo.</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeModal('modal-edit-usuario-{{ $u->id }}')">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Guardar cambios</button>
            </div>
        </form>
    </div>
</div>
