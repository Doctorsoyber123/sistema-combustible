<?php

namespace App\Http\Controllers;

use App\Models\Consumo;
use App\Models\Tramo;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Http\Request;

class ConsumoController extends Controller
{
    public function index(Request $request)
    {
        $vehiculoId = $request->input('vehiculo_id');
        $tramoId    = $request->input('tramo_id');
        $desde      = $request->input('desde');
        $hasta      = $request->input('hasta');

        $query = Consumo::with(['vehiculo', 'tramo'])
            ->when($vehiculoId, fn ($q) => $q->where('vehiculo_id', $vehiculoId))
            ->when($tramoId, fn ($q) => $q->where('tramo_id', $tramoId))
            ->when($desde, fn ($q) => $q->whereDate('fecha', '>=', $desde))
            ->when($hasta, fn ($q) => $q->whereDate('fecha', '<=', $hasta));

        // Total de galones de TODOS los resultados filtrados (no solo la pagina actual)
        $totalGalones = (clone $query)->sum('galones');

        $consumos = $query->latest()->paginate(10)->withQueryString();

        $vehiculos    = Vehiculo::orderBy('codigo')->get();
        $tramos       = Tramo::orderBy('nombre')->get();
        $trabajadores = User::where('role', 'trabajador')->where('activo', true)
                            ->orderBy('name')->get();

        return view('consumos.index', compact(
            'consumos', 'vehiculos', 'tramos', 'trabajadores', 'totalGalones',
            'vehiculoId', 'tramoId', 'desde', 'hasta'
        ));
    }

    public function create()
    {
        $vehiculos    = Vehiculo::where('activo', true)->orderBy('codigo')->get();
        $tramos       = Tramo::where('activo', true)->orderBy('nombre')->get();
        $trabajadores = User::where('role', 'trabajador')->where('activo', true)
                            ->orderBy('name')->get();
        return view('consumos.create', compact('vehiculos', 'tramos', 'trabajadores'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vehiculo_id'   => 'required|exists:vehiculos,id',
            'tramo_id'      => 'required|exists:tramos,id',
            'galones'       => 'required|numeric|min:0.01',
            'fecha'         => 'required|date',
            'operador'      => 'nullable|string|max:150',
            'observaciones' => 'nullable|string',
        ]);

        Consumo::create($data);

        return redirect()->route('consumos.index')
            ->with('success', 'Consumo registrado correctamente.');
    }

    public function update(Request $request, Consumo $consumo)
    {
        $data = $request->validate([
            'vehiculo_id'   => 'required|exists:vehiculos,id',
            'tramo_id'      => 'required|exists:tramos,id',
            'galones'       => 'required|numeric|min:0.01',
            'fecha'         => 'required|date',
            'operador'      => 'nullable|string|max:150',
            'observaciones' => 'nullable|string',
        ]);

        $consumo->update($data);

        return redirect()->route('consumos.index')
            ->with('success', 'Consumo actualizado correctamente.');
    }

    public function destroy(Consumo $consumo)
    {
        $consumo->delete();
        return redirect()->route('consumos.index')
            ->with('success', 'Consumo eliminado.');
    }
}
