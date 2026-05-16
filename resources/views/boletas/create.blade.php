@extends('layouts.app')

@section('title', 'Registrar Boleta')
@section('page-title', 'Registrar boleta')
@section('page-badge', 'Combustible')

@section('content')
<div style="max-width:760px">
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="ti ti-receipt"></i> Registrar boleta de combustible</div>
        </div>
        <div class="card-body">
            @if($vehiculos->isEmpty())
                <div class="alert alert-error">
                    <i class="ti ti-alert-triangle"></i>
                    Necesitas al menos un vehiculo activo antes de registrar boletas.
                </div>
            @endif
            <form action="{{ route('boletas.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label>N&deg; de Boleta</label>
                        <input type="text" name="numero_boleta" value="{{ old('numero_boleta') }}" placeholder="Ej: B-00235" required>
                        @error('numero_boleta') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Fecha</label>
                        <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required>
                        @error('fecha') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
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
                        <label>Proveedor / Grifo</label>
                        <input type="text" name="proveedor" value="{{ old('proveedor') }}" placeholder="Nombre del grifo">
                        @error('proveedor') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Galones</label>
                        <input type="number" id="b-galones" name="galones" value="{{ old('galones') }}"
                               step="0.01" min="0.01" placeholder="0.00" required>
                        @error('galones') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Precio por galon (S/)</label>
                        <input type="number" id="b-precio" name="precio_galon" value="{{ old('precio_galon') }}"
                               step="0.01" min="0.01" placeholder="0.00" required>
                        @error('precio_galon') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group full">
                        <label>Total (S/) &mdash; calculado automaticamente</label>
                        <input type="text" id="b-total" placeholder="0.00" readonly>
                    </div>
                    <div class="form-group full">
                        <label>
                            <i class="ti ti-paperclip" style="vertical-align:-2px"></i>
                            Evidencia &mdash; foto o PDF de la boleta fisica
                        </label>
                        <input type="file" name="evidencia" accept=".jpg,.jpeg,.png,.webp,.pdf,image/*,application/pdf">
                        <span style="font-size:11px;color:var(--text3)">
                            Formatos permitidos: JPG, PNG, WEBP o PDF &middot; tamano maximo 5 MB.
                            Recomendado: adjunta siempre la evidencia, es informacion delicada.
                        </span>
                        @error('evidencia') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="form-actions">
                    <a href="{{ route('boletas.index') }}" class="btn">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Registrar boleta</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const gal = document.getElementById('b-galones');
    const pre = document.getElementById('b-precio');
    const tot = document.getElementById('b-total');
    function calcTotal() {
        const g = parseFloat(gal.value) || 0;
        const p = parseFloat(pre.value) || 0;
        tot.value = (g * p).toFixed(2);
    }
    gal.addEventListener('input', calcTotal);
    pre.addEventListener('input', calcTotal);
    calcTotal();
</script>
@endpush
