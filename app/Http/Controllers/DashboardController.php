<?php

namespace App\Http\Controllers;

use App\Models\Boleta;
use App\Models\Consumo;
use App\Models\Tramo;
use App\Models\Vehiculo;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');
        $hayFiltro = (bool) ($desde || $hasta);

        $consumoQ = Consumo::query()
            ->when($desde, fn ($q) => $q->whereDate('fecha', '>=', $desde))
            ->when($hasta, fn ($q) => $q->whereDate('fecha', '<=', $hasta));

        $boletaQ = Boleta::query()
            ->when($desde, fn ($q) => $q->whereDate('fecha', '>=', $desde))
            ->when($hasta, fn ($q) => $q->whereDate('fecha', '<=', $hasta));

        $totalGalones = (clone $consumoQ)->sum('galones');
        $totalCosto   = (clone $boletaQ)->sum('total');

        if ($hayFiltro) {
            // Con rango de fechas: solo vehiculos y tramos con consumos en ese periodo
            $totalVehiculos = (clone $consumoQ)->distinct()->count('vehiculo_id');
            $totalTramos    = (clone $consumoQ)->distinct()->count('tramo_id');
        } else {
            // Sin filtro: total de vehiculos y tramos activos registrados
            $totalVehiculos = Vehiculo::where('activo', true)->count();
            $totalTramos    = Tramo::where('activo', true)->count();
        }

        $ultimosConsumos = (clone $consumoQ)->with(['vehiculo', 'tramo'])
            ->latest()->take(5)->get();

        $ultimasBoletas = (clone $boletaQ)->with('vehiculo')
            ->latest()->take(5)->get();

        // Galones por vehiculo (para la barra comparativa)
        $consumoPorVehiculo = (clone $consumoQ)
            ->selectRaw('vehiculo_id, SUM(galones) as total_galones')
            ->groupBy('vehiculo_id')
            ->with('vehiculo')
            ->get();

        return view('dashboard.index', compact(
            'totalGalones', 'totalCosto', 'totalVehiculos',
            'totalTramos', 'ultimosConsumos', 'ultimasBoletas',
            'consumoPorVehiculo', 'desde', 'hasta', 'hayFiltro'
        ));
    }

    public function reporteVehiculos(Request $request)
    {
        $tipo  = $request->input('tipo');
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        $rango = function ($q) use ($desde, $hasta) {
            $q->when($desde, fn ($x) => $x->whereDate('fecha', '>=', $desde))
              ->when($hasta, fn ($x) => $x->whereDate('fecha', '<=', $hasta));
        };

        $vehiculos = Vehiculo::query()
            ->when($tipo, fn ($q) => $q->where('tipo', $tipo))
            ->with(['consumos' => $rango, 'boletas' => $rango])
            ->get()
            ->map(function ($v) {
                $v->total_galones = $v->consumos->sum('galones');
                $v->total_costo   = $v->boletas->sum('total');
                $v->total_viajes  = $v->consumos->count();
                return $v;
            });

        $tipos = Vehiculo::select('tipo')->distinct()->orderBy('tipo')->pluck('tipo');

        return view('reportes.vehiculos', compact('vehiculos', 'tipos', 'tipo', 'desde', 'hasta'));
    }

    public function reporteTramos(Request $request)
    {
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        $rango = function ($q) use ($desde, $hasta) {
            $q->when($desde, fn ($x) => $x->whereDate('fecha', '>=', $desde))
              ->when($hasta, fn ($x) => $x->whereDate('fecha', '<=', $hasta));
        };

        $tramos = Tramo::with(['consumos' => $rango, 'consumos.vehiculo'])
            ->get()
            ->map(function ($t) {
                $t->total_galones = $t->consumos->sum('galones');
                // Agrupar consumo por vehiculo dentro de cada tramo
                $t->por_vehiculo = $t->consumos->groupBy('vehiculo_id')->map(function ($cs) {
                    return [
                        'vehiculo'      => $cs->first()->vehiculo,
                        'total_galones' => $cs->sum('galones'),
                        'viajes'        => $cs->count(),
                    ];
                })->values();
                return $t;
            });

        return view('reportes.tramos', compact('tramos', 'desde', 'hasta'));
    }
}
