<?php

namespace App\Http\Controllers;

use App\Models\Boleta;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BoletaController extends Controller
{
    public function index(Request $request)
    {
        $vehiculoId = $request->input('vehiculo_id');
        $proveedor  = $request->input('proveedor');
        $desde      = $request->input('desde');
        $hasta      = $request->input('hasta');

        $query = Boleta::with('vehiculo')
            ->when($vehiculoId, fn ($q) => $q->where('vehiculo_id', $vehiculoId))
            ->when($proveedor, fn ($q) => $q->where('proveedor', 'like', "%{$proveedor}%"))
            ->when($desde, fn ($q) => $q->whereDate('fecha', '>=', $desde))
            ->when($hasta, fn ($q) => $q->whereDate('fecha', '<=', $hasta));

        // Importe total de TODAS las boletas filtradas (no solo la pagina actual)
        $totalImporte = (clone $query)->sum('total');

        $boletas = $query->latest()->paginate(10)->withQueryString();

        $vehiculos = Vehiculo::orderBy('codigo')->get();

        return view('boletas.index', compact(
            'boletas', 'vehiculos', 'totalImporte', 'vehiculoId', 'proveedor', 'desde', 'hasta'
        ));
    }

    public function create()
    {
        $vehiculos = Vehiculo::where('activo', true)->orderBy('codigo')->get();
        return view('boletas.create', compact('vehiculos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'numero_boleta' => 'required|string|max:50',
            'vehiculo_id'   => 'required|exists:vehiculos,id',
            'galones'       => 'required|numeric|min:0.01',
            'precio_galon'  => 'required|numeric|min:0.01',
            'fecha'         => 'required|date',
            'proveedor'     => 'nullable|string|max:150',
            // Evidencia: imagen o PDF de la boleta fisica (max 5 MB)
            'evidencia'     => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ], [], [
            'evidencia' => 'archivo de evidencia',
        ]);

        // El archivo se guarda aparte; no debe ir directo al modelo
        unset($data['evidencia']);

        // Calcular total automaticamente
        $data['total'] = round($data['galones'] * $data['precio_galon'], 2);

        // Guardar la evidencia en storage/app/public/boletas
        if ($request->hasFile('evidencia')) {
            $data['evidencia'] = $request->file('evidencia')->store('boletas', 'public');
        }

        Boleta::create($data);

        return redirect()->route('boletas.index')
            ->with('success', 'Boleta registrada correctamente.');
    }

    public function update(Request $request, Boleta $boleta)
    {
        $data = $request->validate([
            'numero_boleta'      => 'required|string|max:50',
            'vehiculo_id'        => 'required|exists:vehiculos,id',
            'galones'            => 'required|numeric|min:0.01',
            'precio_galon'       => 'required|numeric|min:0.01',
            'fecha'              => 'required|date',
            'proveedor'          => 'nullable|string|max:150',
            'evidencia'          => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            'eliminar_evidencia' => 'nullable|boolean',
        ]);

        $eliminar = $request->boolean('eliminar_evidencia');
        unset($data['evidencia'], $data['eliminar_evidencia']);

        $data['total'] = round($data['galones'] * $data['precio_galon'], 2);

        if ($request->hasFile('evidencia')) {
            // Reemplaza el archivo existente
            if ($boleta->evidencia) {
                Storage::disk('public')->delete($boleta->evidencia);
            }
            $data['evidencia'] = $request->file('evidencia')->store('boletas', 'public');
        } elseif ($eliminar && $boleta->evidencia) {
            Storage::disk('public')->delete($boleta->evidencia);
            $data['evidencia'] = null;
        }

        $boleta->update($data);

        return redirect()->route('boletas.index')
            ->with('success', 'Boleta actualizada correctamente.');
    }

    public function destroy(Boleta $boleta)
    {
        // Eliminar tambien el archivo de evidencia si existe
        if ($boleta->evidencia) {
            Storage::disk('public')->delete($boleta->evidencia);
        }

        $boleta->delete();
        return redirect()->route('boletas.index')
            ->with('success', 'Boleta eliminada.');
    }
}
