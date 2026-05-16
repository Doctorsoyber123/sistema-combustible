@extends('layouts.app')

@section('title', 'Agregar Usuario')
@section('page-title', 'Agregar usuario')
@section('page-badge', 'Maestros')

@section('content')
<div style="max-width:720px">
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="ti ti-user-plus"></i> Nuevo usuario</div>
        </div>
        <div class="card-body">
            <form action="{{ route('usuarios.store') }}" method="POST">
                @csrf
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
                <div class="form-actions">
                    <a href="{{ route('usuarios.index') }}" class="btn">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Crear usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
