@extends('layouts.app')

@section('title', 'Tramos')
@section('page-title', 'Tramos')
@section('page-badge', 'Maestros')

@section('content')
<div class="page-actions">
    <button type="button" class="btn btn-primary" onclick="openModal('modal-tramo')">
        <i class="ti ti-plus"></i> Agregar tramo
    </button>
</div>

<form method="GET" class="filter-bar">
    <div class="filter-field">
        <label>Buscar</label>
        <input type="text" name="q" value="{{ $q }}" placeholder="Nombre, origen o destino..." style="min-width:240px">
    </div>
    <div class="filter-actions">
        <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-filter"></i> Filtrar</button>
        <a href="{{ route('tramos.index') }}" class="btn btn-sm"><i class="ti ti-x"></i> Limpiar</a>
    </div>
</form>

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-route"></i> Tramos registrados</div>
        <span style="font-size:12px;color:var(--text3)">{{ $tramos->total() }} en total</span>
    </div>
    @if($tramos->isEmpty())
        <div class="empty" style="padding:40px"><i class="ti ti-route"></i>Sin tramos aun</div>
    @else
        <div style="padding:0 6px 6px">
            <table>
                <thead>
                    <tr><th>Nombre</th><th>Origen</th><th>Destino</th><th>Distancia</th><th>Galones</th><th></th></tr>
                </thead>
                <tbody>
                    @foreach($tramos as $t)
                        <tr>
                            <td>
                                <strong>{{ $t->nombre }}</strong>
                                @if($t->descripcion)
                                    <div class="text-muted" style="font-size:12px;margin-top:6px">{{ $t->descripcion }}</div>
                                @endif
                            </td>
                            <td>{{ $t->origen ?? '-' }}</td>
                            <td>{{ $t->destino ?? '-' }}</td>
                            <td class="mono">{{ rtrim(rtrim(number_format($t->km, 2), '0'), '.') }} km</td>
                            <td class="mono">{{ $t->galones ? rtrim(rtrim(number_format($t->galones, 2), '0'), '.') . ' gl' : '-' }}</td>
                            <td style="text-align:right;white-space:nowrap">
                                <button type="button" class="btn btn-sm" onclick="openModal('modal-edit-tramo-{{ $t->id }}')" title="Editar">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <form action="{{ route('tramos.destroy', $t) }}" method="POST" style="display:inline"
                                      data-confirm="¿Eliminar el tramo {{ $t->nombre }}? Tambien se borraran sus consumos.">
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
        {{ $tramos->links('pagination.custom') }}
    @endif
</div>

{{-- MODAL: Agregar tramo --}}
<div class="modal-overlay" id="modal-tramo">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-title"><i class="ti ti-map-pin"></i> Nuevo tramo</div>
            <button type="button" class="modal-close" onclick="closeModal('modal-tramo')"><i class="ti ti-x"></i></button>
        </div>
        <form action="{{ route('tramos.store') }}" method="POST" data-confirm="¿Confirmas registrar este tramo?">
            @csrf
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nombre del tramo</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" placeholder="Ej: Cantera - Planta" required>
                        @error('nombre') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Distancia (km)</label>
                        <input type="number" name="km" value="{{ old('km') }}" step="0.1" min="0.1" placeholder="0.0" required>
                        @error('km') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Punto de origen</label>
                        <input type="text" name="origen" value="{{ old('origen') }}" placeholder="Ej: Cantera principal" required>
                        @error('origen') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Punto de destino</label>
                        <input type="text" name="destino" value="{{ old('destino') }}" placeholder="Ej: Planta procesadora" required>
                        @error('destino') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Galones (opcional)</label>
                        <input type="number" name="galones" value="{{ old('galones') }}" step="0.01" min="0" placeholder="0.00">
                        @error('galones') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Turno (opcional)</label>
                        <input type="text" name="turno" value="{{ old('turno') }}" placeholder="Ej: Mañana / Tarde / Noche">
                        @error('turno') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Descripción (opcional)</label>
                        <textarea name="descripcion" rows="3" placeholder="Detalle de la ruta...">{{ old('descripcion') }}</textarea>
                        @error('descripcion') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeModal('modal-tramo')">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Agregar tramo</button>
            </div>
        </form>
    </div>
</div>

{{-- Modales de edicion (uno por cada fila) --}}
@foreach($tramos as $t)
    @include('partials.modal-edit-tramo', ['t' => $t])
@endforeach

@if($errors->any())
    @push('scripts')
        <script>
            @if(old('_edit_id'))
                openModal('modal-edit-tramo-{{ old('_edit_id') }}');
            @else
                openModal('modal-tramo');
            @endif
        </script>
    @endpush
@endif
@endsection
