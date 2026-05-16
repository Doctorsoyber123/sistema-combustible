<?php

namespace App\Http\Controllers;

use App\Models\TipoVehiculo;
use App\Models\Vehiculo;
use Illuminate\Http\Request;

class TipoVehiculoController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100|unique:tipo_vehiculos,nombre',
        ]);

        TipoVehiculo::create($data);

        return redirect()->route('vehiculos.index')
            ->with('success', 'Tipo de vehiculo agregado correctamente.');
    }

    public function destroy(TipoVehiculo $tipoVehiculo)
    {
        // Si hay vehiculos usando este tipo, no se elimina (evita dejarlos sin opcion valida)
        $enUso = Vehiculo::where('tipo', $tipoVehiculo->nombre)->count();

        if ($enUso > 0) {
            return redirect()->route('vehiculos.index')
                ->with('error', "No se puede eliminar '{$tipoVehiculo->nombre}': hay {$enUso} vehiculo(s) con ese tipo.");
        }

        $tipoVehiculo->delete();

        return redirect()->route('vehiculos.index')
            ->with('success', 'Tipo de vehiculo eliminado.');
    }
}
