@extends('layouts.app')

@section('title', 'Trabajadores')
@section('page-title', 'Trabajadores')
@section('page-badge', 'Maestros')

@section('content')
<div class="page-actions">
    <button type="button" class="btn btn-primary" onclick="openModal('modal-usuario')">
        <i class="ti ti-user-plus"></i> Agregar usuario
    </button>
</div>

<form method="GET" class="filter-bar">
    <div class="filter-field">
        <label>Buscar</label>
        <input type="text" name="q" value="{{ $q }}" placeholder="Nombre o usuario...">
    </div>
    <div class="filter-field">
        <label>Rol</label>
        <select name="role">
            <option value="">Todos</option>
            <option value="admin" @selected($role === 'admin')>Administrador</option>
            <option value="trabajador" @selected($role === 'trabajador')>Trabajador</option>
        </select>
    </div>
    <div class="filter-actions">
        <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-filter"></i> Filtrar</button>
        <a href="{{ route('usuarios.index') }}" class="btn btn-sm"><i class="ti ti-x"></i> Limpiar</a>
    </div>
</form>

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-users"></i> Usuarios del sistema</div>
        <span style="font-size:12px;color:var(--text3)">{{ $usuarios->total() }} en total</span>
    </div>
    @if($usuarios->isEmpty())
        <div class="empty" style="padding:40px"><i class="ti ti-users"></i>Sin usuarios para los filtros aplicados</div>
    @else
        <div style="padding:0 6px 6px">
            <table>
                <thead>
                    <tr><th>Nombre</th><th>Usuario</th><th>Rol</th><th>Estado</th><th>Registrado</th><th></th></tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $u)
                        <tr>
                            <td><strong>{{ $u->name }}</strong>
                                @if($u->id === auth()->id())
                                    <span class="chip chip-gray" style="margin-left:4px">tú</span>
                                @endif
                            </td>
                            <td class="mono" style="color:var(--text2)">{{ $u->username }}</td>
                            <td>
                                @if($u->isAdmin())
                                    <span class="chip chip-orange"><i class="ti ti-shield-check"></i> Administrador</span>
                                @else
                                    <span class="chip chip-blue"><i class="ti ti-user"></i> Trabajador</span>
                                @endif
                            </td>
                            <td>
                                @if($u->activo)
                                    <span class="chip chip-green">Activo</span>
                                @else
                                    <span class="chip chip-gray">Inactivo</span>
                                @endif
                            </td>
                            <td style="color:var(--text2)">{{ $u->created_at?->format('d/m/Y') ?? '-' }}</td>
                            <td style="text-align:right;white-space:nowrap">
                                <button type="button" class="btn btn-sm" onclick="openModal('modal-edit-usuario-{{ $u->id }}')" title="Editar">
                                    <i class="ti ti-edit"></i>
                                </button>
                                @if($u->id !== auth()->id())
                                    <form action="{{ route('usuarios.destroy', $u) }}" method="POST" style="display:inline"
                                          data-confirm="¿Eliminar al usuario {{ $u->name }}?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"><i class="ti ti-trash"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $usuarios->links('pagination.custom') }}
    @endif
</div>

{{-- MODAL: Agregar usuario --}}
<div class="modal-overlay" id="modal-usuario">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-title"><i class="ti ti-user-plus"></i> Nuevo usuario</div>
            <button type="button" class="modal-close" onclick="closeModal('modal-usuario')"><i class="ti ti-x"></i></button>
        </div>
        <form action="{{ route('usuarios.store') }}" method="POST" data-confirm="¿Confirmas crear este usuario?">
            @csrf
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nombre completo</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Ej: Carlos Quispe" required>
                        @error('name') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Usuario (para iniciar sesion)</label>
                        <input type="text" name="username" value="{{ old('username') }}" placeholder="Ej: jquispe" required>
                        @error('username') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Contraseña</label>
                        <input type="password" name="password" placeholder="Minimo 4 caracteres" required>
                        @error('password') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" placeholder="Repite la contraseña" required>
                    </div>
                    <div class="form-group full">
                        <label>Rol</label>
                        <select name="role" required>
                            <option value="trabajador" @selected(old('role', 'trabajador') === 'trabajador')>Trabajador &mdash; registra consumos y boletas</option>
                            <option value="admin" @selected(old('role') === 'admin')>Administrador &mdash; acceso total y gestion de maestros</option>
                        </select>
                        @error('role') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeModal('modal-usuario')">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Crear usuario</button>
            </div>
        </form>
    </div>
</div>

{{-- Modales de edicion (uno por cada fila) --}}
@foreach($usuarios as $u)
    @include('partials.modal-edit-usuario', ['u' => $u])
@endforeach

@if($errors->any())
    @push('scripts')
        <script>
            @if(old('_edit_id'))
                openModal('modal-edit-usuario-{{ old('_edit_id') }}');
            @else
                openModal('modal-usuario');
            @endif
        </script>
    @endpush
@endif
@endsection
