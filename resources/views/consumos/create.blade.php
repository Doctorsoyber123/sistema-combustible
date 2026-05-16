@extends('layouts.app')

@section('title', 'Registrar Consumo')
@section('page-title', 'Registrar consumo')
@section('page-badge', 'Nuevo registro')

@section('content')
<div style="max-width:760px">
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="ti ti-gas-station"></i> Nuevo registro de consumo</div>
        </div>
        <div class="card-body">
            @if($vehiculos->isEmpty() || $tramos->isEmpty())
                <div class="alert alert-error">
                    <i class="ti ti-alert-triangle"></i>
                    Necesitas al menos un vehiculo y un tramo activos antes de registrar consumos.
                </div>
            @endif
            <form action="{{ route('consumos.store') }}" method="POST">
                @csrf
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
                        <input type="text" name="operador" value="{{ old('operador') }}" placeholder="Nombre del operador">
                        @error('operador') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Observaciones</label>
                        <input type="text" name="observaciones" value="{{ old('observaciones') }}" placeholder="Opcional...">
                        @error('observaciones') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="form-actions">
                    <a href="{{ route('consumos.index') }}" class="btn">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Registrar consumo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
