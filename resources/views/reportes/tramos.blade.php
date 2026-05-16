@extends('layouts.app')

@section('title', 'Reporte por Tramo')
@section('page-title', 'Reporte')
@section('page-badge', 'Por tramo')

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
    $tramosConDatos = $tramos->filter(fn ($t) => $t->por_vehiculo->isNotEmpty());
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
        <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-filter"></i> Filtrar</button>
        <a href="{{ route('reportes.tramos') }}" class="btn btn-sm"><i class="ti ti-x"></i> Limpiar</a>
    </div>
</form>

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-route"></i> Consumo por tramo</div>
    </div>

    @if($tramosConDatos->isEmpty())
        <div class="empty" style="padding:40px"><i class="ti ti-route"></i>Sin datos para mostrar</div>
    @else
        @foreach($tramosConDatos as $t)
            @php $maxG = $t->por_vehiculo->max('total_galones') ?: 1; @endphp
            <div style="padding:18px;border-bottom:1px solid var(--border)">
                <div style="margin-bottom:14px">
                    <div style="font-size:14px;font-weight:500">
                        <i class="ti ti-route" style="color:var(--accent);font-size:15px;vertical-align:-2px;margin-right:6px"></i>{{ $t->nombre }}
                    </div>
                    <div style="font-size:12px;color:var(--text3);margin-top:2px">
                        {{ $t->origen }} &rarr; {{ $t->destino }}
                        &middot; {{ rtrim(rtrim(number_format($t->km, 2), '0'), '.') }} km
                        &middot; Total: {{ number_format($t->total_galones, 1) }} gal
                    </div>
                </div>
                <table>
                    <thead>
                        <tr><th>Vehiculo</th><th>Tipo</th><th>Galones</th><th>Rendimiento</th><th>Comparativa</th></tr>
                    </thead>
                    <tbody>
                        @foreach($t->por_vehiculo as $fila)
                            @php
                                $v   = $fila['vehiculo'];
                                $gal = (float) $fila['total_galones'];
                                $pct = round($gal / $maxG * 100);
                                $col = $colorTipo[$v->tipo ?? ''] ?? '#8891a8';
                                $galXkm = $t->km > 0 ? number_format($gal / $t->km, 2) : '-';
                            @endphp
                            <tr>
                                <td><strong>{{ $v->codigo ?? '-' }}</strong></td>
                                <td>@include('partials.chip-tipo', ['tipo' => $v->tipo ?? 'Otro'])</td>
                                <td class="mono">{{ number_format($gal, 1) }} gal</td>
                                <td class="mono" style="color:var(--amber)">{{ $galXkm }} gal/km</td>
                                <td style="min-width:150px">
                                    <div class="bar-row">
                                        <div class="bar-bg"><div class="bar-fill" style="width:{{ $pct }}%;background:{{ $col }}"></div></div>
                                        <span class="bar-label">{{ $pct }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    @endif
</div>
@endsection
