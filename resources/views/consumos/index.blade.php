@extends('layouts.app')

@section('title', 'Consumos')
@section('page-title', 'Consumos')
@section('page-badge', 'Registros')

@section('content')
<div class="page-actions">
    <button type="button" class="btn btn-primary" onclick="openModal('modal-consumo')">
        <i class="ti ti-plus"></i> Registrar consumo
    </button>
</div>

<form method="GET" class="filter-bar">
    <div class="filter-field">
        <label>Vehiculo</label>
        <select name="vehiculo_id">
            <option value="">Todos</option>
            @foreach($vehiculos as $v)
                <option value="{{ $v->id }}" @selected($vehiculoId == $v->id)>{{ $v->codigo }} &mdash; {{ $v->tipo }}</option>
            @endforeach
        </select>
    </div>
    <div class="filter-field">
        <label>Tramo</label>
        <select name="tramo_id">
            <option value="">Todos</option>
            @foreach($tramos as $t)
                <option value="{{ $t->id }}" @selected($tramoId == $t->id)>{{ $t->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="filter-field">
        <label>Desde</label>
        <input type="date" name="desde" value="{{ $desde }}">
    </div>
    <div class="filter-field">
        <label>Hasta</label>
        <input type="date" name="hasta" value="{{ $hasta }}">
    </div>
    <div class="filter-actions">
        <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-filter"></i> Filtrar</button>
        <a href="{{ route('consumos.index') }}" class="btn btn-sm"><i class="ti ti-x"></i> Limpiar</a>
    </div>
</form>

{{-- Datalist compartido: sugerencias de operador desde la lista de Trabajadores --}}
<datalist id="trabajadores-list">
    @foreach($trabajadores as $tr)
        <option value="{{ $tr->name }}"></option>
    @endforeach
</datalist>

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-list"></i> Consumos registrados</div>
        <span style="font-size:12px;color:var(--text3)">
            {{ $consumos->total() }} registros &middot; {{ number_format($totalGalones, 1) }} gal
        </span>
    </div>
    @if($consumos->isEmpty())
        <div class="empty" style="padding:40px"><i class="ti ti-database-off"></i>Sin registros aun</div>
    @else
        <div style="padding:0 6px 6px">
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th><th>Vehiculo</th><th>Tipo</th><th>Tramo</th>
                        <th>Galones</th><th>Operador</th><th>Observaciones</th><th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($consumos as $c)
                        <tr>
                            <td>{{ $c->fecha->format('d/m/Y') }}</td>
                            <td><strong>{{ $c->vehiculo->codigo ?? '-' }}</strong></td>
                            <td>@include('partials.chip-tipo', ['tipo' => $c->vehiculo->tipo ?? 'Otro'])</td>
                            <td>{{ $c->tramo->nombre ?? '-' }}</td>
                            <td class="mono">{{ number_format($c->galones, 1) }}</td>
                            <td>{{ $c->operador ?? '-' }}</td>
                            <td style="color:var(--text2)">{{ $c->observaciones ?? '-' }}</td>
                            <td style="text-align:right;white-space:nowrap">
                                <button type="button" class="btn btn-sm" onclick="openModal('modal-edit-consumo-{{ $c->id }}')" title="Editar">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <form action="{{ route('consumos.destroy', $c) }}" method="POST" style="display:inline"
                                      data-confirm="¿Eliminar este consumo registrado?">
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
        {{ $consumos->links('pagination.custom') }}
    @endif
</div>

{{-- MODAL: Registrar consumo --}}
<div class="modal-overlay" id="modal-consumo">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-title"><i class="ti ti-gas-station"></i> Nuevo registro de consumo</div>
            <button type="button" class="modal-close" onclick="closeModal('modal-consumo')"><i class="ti ti-x"></i></button>
        </div>
        @if($vehiculos->isEmpty() || $tramos->isEmpty())
            <div class="modal-body">
                <div class="alert alert-error" style="margin:0">
                    <i class="ti ti-alert-triangle"></i>
                    Necesitas al menos un vehiculo y un tramo antes de registrar consumos.
                </div>
            </div>
        @else
            <form action="{{ route('consumos.store') }}" method="POST" data-confirm="¿Confirmas registrar este consumo?">
                @csrf
                <div class="modal-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Vehiculo</label>
                            <select name="vehiculo_id" required>
                                <option value="">Seleccionar...</option>
                                @foreach($vehiculos as $v)
                                    <option value="{{ $v->id }}" @selected(old('vehiculo_id') == $v->id)>
                                        {{ $v->codigo }} &mdash; {{ $v->tipo }}
                                    </option>
                                @endforeach
                            </select>
                            @error('vehiculo_id') <span class="field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>Tramo</label>
                            <select name="tramo_id" required>
                                <option value="">Seleccionar...</option>
                                @foreach($tramos as $t)
                                    <option value="{{ $t->id }}" @selected(old('tramo_id') == $t->id)>
                                        {{ $t->nombre }} ({{ rtrim(rtrim(number_format($t->km, 2), '0'), '.') }} km)
                                    </option>
                                @endforeach
                            </select>
                            @error('tramo_id') <span class="field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>Galones usados</label>
                            <input type="number" name="galones" value="{{ old('galones') }}" step="0.01" min="0.01" placeholder="0.00" required>
                            @error('galones') <span class="field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>Fecha</label>
                            <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required>
                            @error('fecha') <span class="field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>Operador / Chofer</label>
                            <input type="text" name="operador" list="trabajadores-list" autocomplete="off"
                                   value="{{ old('operador') }}" placeholder="Escribe para buscar trabajador...">
                            <span style="font-size:11px;color:var(--text3)">Sugerencias desde los trabajadores registrados.</span>
                            @error('operador') <span class="field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>Observaciones</label>
                            <input type="text" name="observaciones" value="{{ old('observaciones') }}" placeholder="Opcional...">
                            @error('observaciones') <span class="field-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn" onclick="closeModal('modal-consumo')">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Registrar consumo</button>
                </div>
            </form>
        @endif
    </div>
</div>

{{-- Modales de edicion (uno por cada fila) --}}
@foreach($consumos as $c)
    @include('partials.modal-edit-consumo', ['c' => $c])
@endforeach

@if($errors->any())
    @push('scripts')
        <script>
            @if(old('_edit_id'))
                openModal('modal-edit-consumo-{{ old('_edit_id') }}');
            @else
                openModal('modal-consumo');
            @endif
        </script>
    @endpush
@endif
@endsection
