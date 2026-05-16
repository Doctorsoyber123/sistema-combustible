@extends('layouts.app')

@section('title', 'Agregar Vehiculo')
@section('page-title', 'Agregar vehiculo')
@section('page-badge', 'Configuracion')

@section('content')
<div style="max-width:720px">
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="ti ti-truck"></i> Nuevo vehiculo</div>
        </div>
        <div class="card-body">
            <form action="{{ route('vehiculos.store') }}" method="POST">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label>Codigo</label>
                        <input type="text" name="codigo" value="{{ old('codigo') }}" placeholder="Ej: RX-01, CAM-03" required>
                        @error('codigo') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Tipo de vehiculo</label>
                        <select name="tipo" required>
                            <option value="">Seleccionar...</option>
                            @foreach(['Retroexcavadora', 'Camión', 'Volquete', 'Cargador frontal', 'Motoniveladora', 'Otro'] as $tipo)
                                <option value="{{ $tipo }}" @selected(old('tipo') === $tipo)>{{ $tipo }}</option>
                            @endforeach
                        </select>
                        @error('tipo') <span class="field-error">{{ $message }}</span> @enderror
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
                <div class="form-actions">
                    <a href="{{ route('vehiculos.index') }}" class="btn">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Agregar vehiculo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
