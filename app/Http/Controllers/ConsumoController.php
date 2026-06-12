<?php

namespace App\Http\Controllers;

use App\Models\Consumo;
use App\Models\Boleta;
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
        $perPage    = in_array($request->input('per_page'), ['10','25','50']) ? (int)$request->input('per_page') : 10;

        $query = Consumo::with(['vehiculo', 'tramo'])
            ->when($vehiculoId, fn ($q) => $q->where('vehiculo_id', $vehiculoId))
            ->when($tramoId, fn ($q) => $q->where('tramo_id', $tramoId))
            ->when($desde, fn ($q) => $q->whereDate('fecha', '>=', $desde))
            ->when($hasta, fn ($q) => $q->whereDate('fecha', '<=', $hasta));

        // Total de galones de TODOS los resultados filtrados (no solo la pagina actual)
        $totalGalones = (clone $query)->sum('galones');

        $consumos = $query->latest()->paginate($perPage)->withQueryString();

        $vehiculos    = Vehiculo::orderBy('codigo')->get();
        $tramos       = Tramo::orderBy('nombre')->get();
        $trabajadores = User::where('role', 'trabajador')->where('activo', true)
                    ->orderBy('name')->get();
        $boletas = Boleta::orderBy('fecha', 'desc')->limit(200)->get();
        $boletasDisponibles = $boletas;

        return view('consumos.index', compact(
            'consumos', 'vehiculos', 'tramos', 'trabajadores', 'totalGalones',
            'vehiculoId', 'tramoId', 'desde', 'hasta', 'boletas', 'boletasDisponibles', 'perPage'
        ));
    }

    public function create()
    {
        $vehiculos    = Vehiculo::where('activo', true)->orderBy('codigo')->get();
        $tramos       = Tramo::where('activo', true)->orderBy('nombre')->get();
        $trabajadores = User::where('role', 'trabajador')->where('activo', true)
                            ->orderBy('name')->get();
        $boletas = Boleta::orderBy('fecha', 'desc')->limit(50)->get();
        return view('consumos.create', compact('vehiculos', 'tramos', 'trabajadores', 'boletas'));
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

        // crear consumo primero
        $consumo = Consumo::create($data);

        // asociar boletas existentes (multi select)
        if ($request->filled('boletas_existing')) {
            $existing = array_filter((array)$request->input('boletas_existing'));
            $consumo->boletas()->syncWithoutDetaching($existing);
        }

        // crear nuevas boletas (repeatable rows)
        if ($request->has('new_boletas') && is_array($request->input('new_boletas'))) {
            foreach ($request->input('new_boletas') as $nb) {
                $numero = $nb['numero'] ?? null;
                $proveedor = $nb['proveedor'] ?? null;
                $gal = isset($nb['galones']) && $nb['galones'] !== '' ? (float)$nb['galones'] : null;
                $precio = isset($nb['precio']) && $nb['precio'] !== '' ? (float)$nb['precio'] : null;
                $fechaB = $nb['fecha'] ?? null;
                if (!$numero && !$gal && !$proveedor) {
                    continue; // fila vacía
                }
                $boleta = Boleta::create([
                    'numero_boleta' => $numero,
                    'vehiculo_id'   => $data['vehiculo_id'],
                    'proveedor'     => $proveedor,
                    'galones'       => $gal ?? 0,
                    'precio_galon'  => $precio,
                    'total'         => isset($gal, $precio) ? round($gal * $precio, 2) : null,
                    'fecha'         => $fechaB ?? $data['fecha'],
                ]);
                $consumo->boletas()->attach($boleta->id);
            }
        }

        return redirect()->route('consumos.index')
            ->with('success', 'Consumo registrado correctamente.');

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

        // asociar boletas existentes
        if ($request->filled('boletas_existing')) {
            $existing = array_filter((array)$request->input('boletas_existing'));
            $consumo->boletas()->syncWithoutDetaching($existing);
        }

        // crear/adjuntar nuevas boletas
        if ($request->has('new_boletas') && is_array($request->input('new_boletas'))) {
            foreach ($request->input('new_boletas') as $nb) {
                $numero = $nb['numero'] ?? null;
                $proveedor = $nb['proveedor'] ?? null;
                $gal = isset($nb['galones']) && $nb['galones'] !== '' ? (float)$nb['galones'] : null;
                $precio = isset($nb['precio']) && $nb['precio'] !== '' ? (float)$nb['precio'] : null;
                $fechaB = $nb['fecha'] ?? null;
                if (!$numero && !$gal && !$proveedor) {
                    continue;
                }
                $boleta = Boleta::create([
                    'numero_boleta' => $numero,
                    'vehiculo_id'   => $data['vehiculo_id'],
                    'proveedor'     => $proveedor,
                    'galones'       => $gal ?? 0,
                    'precio_galon'  => $precio,
                    'total'         => isset($gal, $precio) ? round($gal * $precio, 2) : null,
                    'fecha'         => $fechaB ?? $data['fecha'],
                ]);
                $consumo->boletas()->attach($boleta->id);
            }
        }

        return redirect()->route('consumos.index')
            ->with('success', 'Consumo actualizado correctamente.');

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
