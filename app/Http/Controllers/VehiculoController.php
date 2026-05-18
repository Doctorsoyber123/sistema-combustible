<?php

namespace App\Http\Controllers;

use App\Models\TipoVehiculo;
use App\Models\Vehiculo;
use Illuminate\Http\Request;

class VehiculoController extends Controller
{
    public function index(Request $request)
    {
        $q      = $request->input('q');
        $tipo   = $request->input('tipo');
        $estado = $request->input('estado'); // '1' activo, '0' inactivo, '' todos

        $vehiculos = Vehiculo::query()
            ->when($q, fn ($query) => $query->where(fn ($w) =>
                $w->whereRaw('LOWER(codigo) LIKE LOWER(?)', ["%{$q}%"])
                  ->orWhereRaw('LOWER(placa) LIKE LOWER(?)', ["%{$q}%"])
                  ->orWhereRaw('LOWER(modelo) LIKE LOWER(?)', ["%{$q}%"])))
            ->when($tipo, fn ($query) => $query->where('tipo', $tipo))
            ->when($estado !== null && $estado !== '', fn ($query) => $query->where('activo', (bool) $estado))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $tipos = Vehiculo::select('tipo')->distinct()->orderBy('tipo')->pluck('tipo');

        // Catalogo de tipos de vehiculo (para el select y la gestion de tipos)
        $tiposVehiculo = TipoVehiculo::orderBy('nombre')->get();

        return view('vehiculos.index', compact('vehiculos', 'tipos', 'tiposVehiculo', 'q', 'tipo', 'estado'));
    }

    public function create()
    {
        return view('vehiculos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo' => 'required|string|max:50',
            'tipo'   => 'required|string|max:100',
            'placa'  => 'nullable|string|max:20',
            'modelo' => 'nullable|string|max:100',
        ]);

        Vehiculo::create($data);

        return redirect()->route('vehiculos.index')
            ->with('success', 'Vehiculo registrado correctamente.');
    }

    public function update(Request $request, Vehiculo $vehiculo)
    {
        $data = $request->validate([
            'codigo' => 'required|string|max:50',
            'tipo'   => 'required|string|max:100',
            'placa'  => 'nullable|string|max:20',
            'modelo' => 'nullable|string|max:100',
            'activo' => 'nullable|boolean',
        ]);

        $data['activo'] = $request->boolean('activo', $vehiculo->activo);

        $vehiculo->update($data);

        return redirect()->route('vehiculos.index')
            ->with('success', 'Vehiculo actualizado correctamente.');
    }

    public function destroy(Vehiculo $vehiculo)
    {
        $vehiculo->delete();
        return redirect()->route('vehiculos.index')
            ->with('success', 'Vehiculo eliminado.');
    }
}
