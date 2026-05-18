@extends('layouts.app')

@section('title', 'Boletas')
@section('page-title', 'Boletas')
@section('page-badge', 'Combustible')

@section('content')
<div class="page-actions">
    <button type="button" class="btn btn-primary" onclick="openModal('modal-boleta')">
        <i class="ti ti-plus"></i> Registrar boleta
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
        <label>Proveedor</label>
        <input type="text" name="proveedor" value="{{ $proveedor }}" placeholder="Nombre del grifo...">
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
        <a href="{{ route('boletas.index') }}" class="btn btn-sm"><i class="ti ti-x"></i> Limpiar</a>
    </div>
</form>

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-receipt"></i> Boletas registradas</div>
        <span style="font-size:12px;color:var(--text3)">
            {{ $boletas->total() }} boletas &middot; S/ {{ number_format($totalImporte, 2) }}
        </span>
    </div>
    @if($boletas->isEmpty())
        <div class="empty" style="padding:40px"><i class="ti ti-receipt-off"></i>Sin boletas aun</div>
    @else
        <div style="padding:0 6px 6px">
            <table>
                <thead>
                    <tr>
                        <th>N&deg; Boleta</th><th>Fecha</th><th>Vehiculo</th><th>Proveedor</th>
                        <th>Galones</th><th>Precio/gal</th><th>Total</th><th>Evidencia</th><th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($boletas as $b)
                        <tr>
                            <td><strong>{{ $b->numero_boleta }}</strong></td>
                            <td>{{ $b->fecha->format('d/m/Y') }}</td>
                            <td>{{ $b->vehiculo->codigo ?? '-' }}</td>
                            <td>{{ $b->proveedor ?? '-' }}</td>
                            <td class="mono">{{ number_format($b->galones, 1) }}</td>
                            <td class="mono">S/ {{ number_format($b->precio_galon, 2) }}</td>
                            <td class="mono" style="color:var(--green)">S/ {{ number_format($b->total, 2) }}</td>
                            <td>
                                @if($b->evidencia)
                                    @php $esPdf = \Illuminate\Support\Str::endsWith(strtolower($b->evidencia), '.pdf'); @endphp
                                    <a href="{{ asset('storage/' . $b->evidencia) }}" target="_blank" rel="noopener"
                                       class="chip chip-blue" style="text-decoration:none">
                                        <i class="ti ti-{{ $esPdf ? 'file-type-pdf' : 'photo' }}"></i> Ver
                                    </a>
                                @else
                                    <span class="chip chip-gray"><i class="ti ti-paperclip-off"></i> Sin archivo</span>
                                @endif
                            </td>
                            <td style="text-align:right;white-space:nowrap">
                                <button type="button" class="btn btn-sm" onclick="openModal('modal-edit-boleta-{{ $b->id }}')" title="Editar">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <form action="{{ route('boletas.destroy', $b) }}" method="POST" style="display:inline"
                                      data-confirm="¿Eliminar la boleta {{ $b->numero_boleta }}? Tambien se borrara su evidencia.">
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
        {{ $boletas->links('pagination.custom') }}
    @endif
</div>

{{-- MODAL: Registrar boleta --}}
<div class="modal-overlay" id="modal-boleta">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-title"><i class="ti ti-receipt"></i> Registrar boleta de combustible</div>
            <button type="button" class="modal-close" onclick="closeModal('modal-boleta')"><i class="ti ti-x"></i></button>
        </div>
        @if($vehiculos->isEmpty())
            <div class="modal-body">
                <div class="alert alert-error" style="margin:0">
                    <i class="ti ti-alert-triangle"></i>
                    Necesitas al menos un vehiculo antes de registrar boletas.
                </div>
            </div>
        @else
            <form action="{{ route('boletas.store') }}" method="POST" enctype="multipart/form-data"
                  data-confirm="¿Confirmas registrar esta boleta?">
                @csrf
                <div class="modal-body">
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
                            <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
                                <label class="btn" style="cursor:pointer;margin:0">
                                    <i class="ti ti-camera"></i> Tomar foto
                                    <input type="file" name="evidencia_camara" accept="image/*" capture="environment"
                                           style="display:none" onchange="mostrarNombreEvidencia(this,'ev-nombre-new')">
                                </label>
                                <label class="btn" style="cursor:pointer;margin:0">
                                    <i class="ti ti-paperclip"></i> Elegir archivo
                                    <input type="file" name="evidencia" accept=".jpg,.jpeg,.png,.webp,.pdf,image/*,application/pdf"
                                           style="display:none" onchange="mostrarNombreEvidencia(this,'ev-nombre-new')">
                                </label>
                                <span id="ev-nombre-new" style="font-size:12.5px;color:var(--text2)"></span>
                            </div>
                            <span style="font-size:11px;color:var(--text3)">
                                En el celular, "Tomar foto" abre la camara directo. Formatos: JPG/PNG/WEBP/PDF &middot; maximo 5 MB.
                            </span>
                            @error('evidencia') <span class="field-error">{{ $message }}</span> @enderror
                            @error('evidencia_camara') <span class="field-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn" onclick="closeModal('modal-boleta')">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Registrar boleta</button>
                </div>
            </form>
        @endif
    </div>
</div>

{{-- Modales de edicion (uno por cada fila) --}}
@foreach($boletas as $b)
    @include('partials.modal-edit-boleta', ['b' => $b])
@endforeach

@push('scripts')
<script>
    // Calculo del total en el modal de registro
    (function () {
        var gal = document.getElementById('b-galones');
        var pre = document.getElementById('b-precio');
        var tot = document.getElementById('b-total');
        function calcTotal() {
            var g = parseFloat(gal.value) || 0;
            var p = parseFloat(pre.value) || 0;
            tot.value = (g * p).toFixed(2);
        }
        if (gal && pre && tot) {
            gal.addEventListener('input', calcTotal);
            pre.addEventListener('input', calcTotal);
            calcTotal();
        }
    })();
    // Cuando se elige un archivo (o se toma una foto), mostrar el nombre y limpiar el otro input
    function mostrarNombreEvidencia(input, spanId) {
        var span = document.getElementById(spanId);
        if (input.files && input.files.length) {
            span.innerHTML = '<i class="ti ti-check" style="color:var(--green);vertical-align:-2px"></i> ' + input.files[0].name;
            var form = input.closest('form');
            if (form) {
                form.querySelectorAll('input[type=file]').forEach(function (other) {
                    if (other !== input && other.value) other.value = '';
                });
            }
        } else {
            span.textContent = '';
        }
    }
    // Calculo del total para los modales de edicion (uno por fila)
    function calcEditTotal(id) {
        var g = parseFloat(document.getElementById('edit-b-galones-' + id).value) || 0;
        var p = parseFloat(document.getElementById('edit-b-precio-' + id).value) || 0;
        document.getElementById('edit-b-total-' + id).value = (g * p).toFixed(2);
    }
    @if($errors->any())
        @if(old('_edit_id'))
            openModal('modal-edit-boleta-{{ old('_edit_id') }}');
        @else
            openModal('modal-boleta');
        @endif
    @endif
</script>
@endpush
@endsection
