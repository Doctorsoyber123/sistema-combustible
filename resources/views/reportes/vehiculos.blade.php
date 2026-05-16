@extends('layouts.app')

@section('title', 'Reporte por Vehiculo')
@section('page-title', 'Reporte')
@section('page-badge', 'Por vehiculo')

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
    $maxGal = $vehiculos->max('total_galones') ?: 1;
@endphp

<form method="GET" class="filter-bar">
    <div class="filter-field">
        <label>Tipo de vehiculo</label>
        <select name="tipo">
            <option value="">Todos</option>
            @foreach($tipos as $tp)
                <option value="{{ $tp }}" @selected($tipo === $tp)>{{ $tp }}</option>
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
        <a href="{{ route('reportes.vehiculos') }}" class="btn btn-sm"><i class="ti ti-x"></i> Limpiar</a>
    </div>
</form>

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-chart-bar"></i> Comparativa de consumo por vehiculo</div>
    </div>
    @if($vehiculos->isEmpty())
        <div class="empty" style="padding:40px"><i class="ti ti-chart-bar"></i>Sin datos para mostrar</div>
    @else
        <div style="padding:0 6px 6px">
            <table>
                <thead>
                    <tr>
                        <th>Vehiculo</th><th>Tipo</th><th>Placa</th>
                        <th>Total galones</th><th>Costo total</th><th>Viajes</th><th>Uso relativo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vehiculos as $v)
                        @php
                            $gal = (float) $v->total_galones;
                            $pct = round($gal / $maxGal * 100);
                            $col = $colorTipo[$v->tipo] ?? '#8891a8';
                        @endphp
                        <tr>
                            <td><strong>{{ $v->codigo }}</strong></td>
                            <td>@include('partials.chip-tipo', ['tipo' => $v->tipo])</td>
                            <td>{{ $v->placa ?? '-' }}</td>
                            <td class="mono">{{ number_format($gal, 1) }} gal</td>
                            <td class="mono" style="color:var(--green)">S/ {{ number_format($v->total_costo, 2) }}</td>
                            <td>{{ $v->total_viajes }} viajes</td>
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
    @endif
</div>
@endsection
