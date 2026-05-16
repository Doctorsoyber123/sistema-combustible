@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-badge', 'General')

@section('content')
@php
    $colorTipo = [
        'Retroexcavadora'  => '#f97316',
        'Camion'           => '#3b82f6',
        'Camión'           => '#3b82f6',
        'Volquete'         => '#3b82f6',
        'Cargador frontal' => '#22c55e',
        'Motoniveladora'   => '#fb923c',
        'Otro'             => '#8891a8',
    ];
    $maxGal = $consumoPorVehiculo->max('total_galones') ?: 1;
@endphp

<form method="GET" class="filter-bar">
    <div class="filter-field">
        <label>Desde</label>
        <input type="date" name="desde" value="{{ $desde }}">
    </div>
    <div class="filter-field">
        <label>Hasta</label>
        <input type="date" name="hasta" value="{{ $hasta }}">
    </div>
    <div class="filter-actions">
        <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-filter"></i> Aplicar periodo</button>
        <a href="{{ route('dashboard') }}" class="btn btn-sm"><i class="ti ti-x"></i> Limpiar</a>
    </div>
</form>

{{-- STATS --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(249,115,22,0.15)"><i class="ti ti-droplet" style="color:var(--accent)"></i></div>
        <div class="stat-label">Total galones</div>
        <div class="stat-val">{{ number_format($totalGalones, 1) }}</div>
        <div class="stat-sub">consumidos</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(34,197,94,0.15)"><i class="ti ti-currency-dollar" style="color:var(--green)"></i></div>
        <div class="stat-label">Costo total</div>
        <div class="stat-val">S/ {{ number_format($totalCosto, 2) }}</div>
        <div class="stat-sub">en boletas</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(59,130,246,0.15)"><i class="ti ti-truck" style="color:var(--blue)"></i></div>
        <div class="stat-label">{{ $hayFiltro ? 'Vehiculos con actividad' : 'Vehiculos activos' }}</div>
        <div class="stat-val">{{ $totalVehiculos }}</div>
        <div class="stat-sub">{{ $hayFiltro ? 'con consumos en el periodo' : 'registrados' }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(245,158,11,0.15)"><i class="ti ti-route" style="color:var(--amber)"></i></div>
        <div class="stat-label">{{ $hayFiltro ? 'Tramos con actividad' : 'Tramos' }}</div>
        <div class="stat-val">{{ $totalTramos }}</div>
        <div class="stat-sub">{{ $hayFiltro ? 'con consumos en el periodo' : 'configurados' }}</div>
    </div>
</div>

<div class="row2">
    {{-- CONSUMO POR VEHICULO --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="ti ti-chart-bar"></i> Consumo por vehiculo</div>
        </div>
        <div class="card-body">
            @forelse($consumoPorVehiculo as $row)
                @php
                    $g   = (float) $row->total_galones;
                    $pct = round($g / $maxGal * 100);
                    $col = $colorTipo[$row->vehiculo->tipo ?? ''] ?? '#8891a8';
                @endphp
                <div style="margin-bottom:14px">
                    <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:5px">
                        <span>{{ $row->vehiculo->codigo ?? '-' }}
                            <span style="color:var(--text3)">{{ $row->vehiculo->tipo ?? '' }}</span></span>
                        <span class="mono" style="color:var(--text2)">{{ number_format($g, 1) }} gal</span>
                    </div>
                    <div class="bar-row">
                        <div class="bar-bg"><div class="bar-fill" style="width:{{ $pct }}%;background:{{ $col }}"></div></div>
                        <span class="bar-label">{{ $pct }}%</span>
                    </div>
                </div>
            @empty
                <div class="empty"><i class="ti ti-database-off"></i>Sin datos aun</div>
            @endforelse
        </div>
    </div>

    {{-- ULTIMAS BOLETAS --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="ti ti-receipt"></i> Ultimas boletas</div>
        </div>
        <div class="card-body">
            @forelse($ultimasBoletas as $b)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0;border-bottom:1px solid var(--border)">
                    <div>
                        <div style="font-size:13px">{{ $b->numero_boleta }}</div>
                        <div style="font-size:11px;color:var(--text3)">
                            {{ $b->vehiculo->codigo ?? '-' }} &middot; {{ $b->fecha->format('d/m/Y') }}
                        </div>
                    </div>
                    <div class="mono" style="font-size:13px;font-weight:500;color:var(--green)">S/ {{ number_format($b->total, 2) }}</div>
                </div>
            @empty
                <div class="empty"><i class="ti ti-receipt-off"></i>Sin boletas aun</div>
            @endforelse
        </div>
    </div>
</div>

{{-- ULTIMOS CONSUMOS --}}
<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-list-details"></i> Ultimos consumos registrados</div>
        <a href="{{ route('consumos.index') }}" class="btn btn-primary btn-sm"><i class="ti ti-plus"></i> Registrar consumo</a>
    </div>
    @if($ultimosConsumos->isEmpty())
        <div class="empty" style="padding:30px"><i class="ti ti-database-off"></i>Sin consumos aun</div>
    @else
        <div style="padding:0 6px 6px">
            <table>
                <thead>
                    <tr><th>Fecha</th><th>Vehiculo</th><th>Tipo</th><th>Tramo</th><th>Galones</th><th>Operador</th></tr>
                </thead>
                <tbody>
                    @foreach($ultimosConsumos as $c)
                        <tr>
                            <td>{{ $c->fecha->format('d/m/Y') }}</td>
                            <td>{{ $c->vehiculo->codigo ?? '-' }}</td>
                            <td>@include('partials.chip-tipo', ['tipo' => $c->vehiculo->tipo ?? 'Otro'])</td>
                            <td>{{ $c->tramo->nombre ?? '-' }}</td>
                            <td class="mono">{{ number_format($c->galones, 1) }} gal</td>
                            <td style="color:var(--text2)">{{ $c->operador ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
