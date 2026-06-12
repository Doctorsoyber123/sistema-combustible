@extends('layouts.app')

@section('title', 'Agregar Tramo')
@section('page-title', 'Agregar tramo')
@section('page-badge', 'Configuracion')

@section('content')
<div style="max-width:720px">
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="ti ti-map-pin"></i> Nuevo tramo</div>
        </div>
        <div class="card-body">
            <form action="{{ route('tramos.store') }}" method="POST">
                @csrf
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
                        <label>Galones</label>
                        <input type="number" name="galones" value="{{ old('galones') }}" step="0.01" min="0" placeholder="0.00" required>
                        @error('galones') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="form-actions">
                    <a href="{{ route('tramos.index') }}" class="btn">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Agregar tramo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
