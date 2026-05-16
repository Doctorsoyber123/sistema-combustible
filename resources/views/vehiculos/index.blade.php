@extends('layouts.app')

@section('title', 'Vehiculos')
@section('page-title', 'Vehiculos')
@section('page-badge', 'Maestros')

@section('content')
<div class="page-actions" style="gap:8px">
    <button type="button" class="btn" onclick="openModal('modal-tipos')">
        <i class="ti ti-category"></i> Tipos de vehiculo
    </button>
    <button type="button" class="btn btn-primary" onclick="openModal('modal-vehiculo')">
        <i class="ti ti-plus"></i> Agregar vehiculo
    </button>
</div>

<form method="GET" class="filter-bar">
    <div class="filter-field">
        <label>Buscar</label>
        <input type="text" name="q" value="{{ $q }}" placeholder="Codigo, placa o modelo...">
    </div>
    <div class="filter-field">
        <label>Tipo</label>
        <select name="tipo">
            <option value="">Todos</option>
            @foreach($tipos as $tp)
                <option value="{{ $tp }}" @selected($tipo === $tp)>{{ $tp }}</option>
            @endforeach
        </select>
    </div>
    <div class="filter-field">
        <label>Estado</label>
        <select name="estado">
            <option value="">Todos</option>
            <option value="1" @selected($estado === '1')>Activo</option>
            <option value="0" @selected($estado === '0')>Inactivo</option>
        </select>
    </div>
    <div class="filter-actions">
        <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-filter"></i> Filtrar</button>
        <a href="{{ route('vehiculos.index') }}" class="btn btn-sm"><i class="ti ti-x"></i> Limpiar</a>
    </div>
</form>

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-truck"></i> Vehiculos registrados</div>
        <span style="font-size:12px;color:var(--text3)">{{ $vehiculos->total() }} en total</span>
    </div>
    @if($vehiculos->isEmpty())
        <div class="empty" style="padding:40px"><i class="ti ti-truck"></i>Sin vehiculos aun</div>
    @else
        <div style="padding:0 6px 6px">
            <table>
                <thead>
                    <tr><th>Codigo</th><th>Tipo</th><th>Placa</th><th>Modelo</th><th>Estado</th><th></th></tr>
                </thead>
                <tbody>
                    @foreach($vehiculos as $v)
                        <tr>
                            <td><strong>{{ $v->codigo }}</strong></td>
                            <td>@include('partials.chip-tipo', ['tipo' => $v->tipo])</td>
                            <td>{{ $v->placa ?? '-' }}</td>
                            <td>{{ $v->modelo ?? '-' }}</td>
                            <td>
                                @if($v->activo)
                                    <span class="chip chip-green">Activo</span>
                                @else
                                    <span class="chip chip-gray">Inactivo</span>
                                @endif
                            </td>
                            <td style="text-align:right;white-space:nowrap">
                                <button type="button" class="btn btn-sm" onclick="openModal('modal-edit-vehiculo-{{ $v->id }}')" title="Editar">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <form action="{{ route('vehiculos.destroy', $v) }}" method="POST" style="display:inline"
                                      data-confirm="¿Eliminar el vehiculo {{ $v->codigo }}? Tambien se borraran sus consumos y boletas.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"><i class="ti ti-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $vehiculos->links('pagination.custom') }}
    @endif
</div>

{{-- MODAL: Agregar vehiculo --}}
<div class="modal-overlay" id="modal-vehiculo">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-title"><i class="ti ti-truck"></i> Nuevo vehiculo</div>
            <button type="button" class="modal-close" onclick="closeModal('modal-vehiculo')"><i class="ti ti-x"></i></button>
        </div>
        <form action="{{ route('vehiculos.store') }}" method="POST" data-confirm="¿Confirmas registrar este vehiculo?">
            @csrf
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Codigo</label>
                        <input type="text" name="codigo" value="{{ old('codigo') }}" placeholder="Ej: RX-01, CAM-03" required>
                        @error('codigo') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Tipo de vehiculo</label>
                        @if($tiposVehiculo->isEmpty())
                            <input type="text" value="Primero agrega un tipo de vehiculo" readonly>
                        @else
                            <select name="tipo" required>
                                <option value="">Seleccionar...</option>
                                @foreach($tiposVehiculo as $tv)
                                    <option value="{{ $tv->nombre }}" @selected(old('tipo') === $tv->nombre)>{{ $tv->nombre }}</option>
                                @endforeach
                            </select>
                        @endif
                        @error('tipo') <span class="field-error">{{ $message }}</span> @enderror
                        <span style="font-size:11px;color:var(--text3)">
                            ¿No encuentras el tipo? Cierra y usa el boton "Tipos de vehiculo".
                        </span>
                    </div>
                    <div class="form-group">
                        <label>Placa</label>
                        <input type="text" name="placa" value="{{ old('placa') }}" placeholder="ABC-123">
                        @error('placa') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Modelo</label>
                        <input type="text" name="modelo" value="{{ old('modelo') }}" placeholder="Ej: CAT 320">
                        @error('modelo') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeModal('modal-vehiculo')">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Agregar vehiculo</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Tipos de vehiculo --}}
<div class="modal-overlay" id="modal-tipos">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-title"><i class="ti ti-category"></i> Tipos de vehiculo</div>
            <button type="button" class="modal-close" onclick="closeModal('modal-tipos')"><i class="ti ti-x"></i></button>
        </div>
        <div class="modal-body">
            {{-- Form: agregar tipo --}}
            <form action="{{ route('tipos-vehiculo.store') }}" method="POST"
                  data-confirm="¿Agregar este tipo de vehiculo?"
                  style="display:flex;gap:10px;align-items:flex-start;margin-bottom:18px">
                @csrf
                <div class="form-group" style="flex:1">
                    <label>Nuevo tipo</label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" placeholder="Ej: Grua, Cisterna, Tractor..." required>
                    @error('nombre') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top:24px">
                    <i class="ti ti-plus"></i> Agregar
                </button>
            </form>

            {{-- Lista de tipos existentes --}}
            @if($tiposVehiculo->isEmpty())
                <div class="empty" style="padding:24px"><i class="ti ti-category"></i>Aun no hay tipos registrados</div>
            @else
                <table>
                    <thead>
                        <tr><th>Tipo de vehiculo</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach($tiposVehiculo as $tv)
                            <tr>
                                <td><strong>{{ $tv->nombre }}</strong></td>
                                <td style="text-align:right">
                                    <form action="{{ route('tipos-vehiculo.destroy', $tv) }}" method="POST"
                                          data-confirm="¿Eliminar el tipo '{{ $tv->nombre }}'?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="ti ti-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        <div class="modal-foot">
            <button type="button" class="btn" onclick="closeModal('modal-tipos')">Cerrar</button>
        </div>
    </div>
</div>

{{-- Modales de edicion (uno por cada fila) --}}
@foreach($vehiculos as $v)
    @include('partials.modal-edit-vehiculo', ['v' => $v])
@endforeach

@if($errors->any())
    @push('scripts')
        <script>
            @if(old('_edit_id'))
                openModal('modal-edit-vehiculo-{{ old('_edit_id') }}');
            @elseif($errors->has('nombre'))
                openModal('modal-tipos');
            @else
                openModal('modal-vehiculo');
            @endif
        </script>
    @endpush
@endif
@endsection
