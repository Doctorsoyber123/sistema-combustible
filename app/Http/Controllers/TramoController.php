<?php

namespace App\Http\Controllers;

use App\Models\Tramo;
use Illuminate\Http\Request;

class TramoController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');

        $tramos = Tramo::query()
            ->when($q, fn ($query) => $query->where(fn ($w) =>
                $w->whereRaw('LOWER(nombre) LIKE LOWER(?)', ["%{$q}%"])
                  ->orWhereRaw('LOWER(origen) LIKE LOWER(?)', ["%{$q}%"])
                  ->orWhereRaw('LOWER(destino) LIKE LOWER(?)', ["%{$q}%"])))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('tramos.index', compact('tramos', 'q'));
    }

    public function create()
    {
        return view('tramos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'  => 'required|string|max:150',
            'origen'  => 'required|string|max:150',
            'destino' => 'required|string|max:150',
            'km'      => 'required|numeric|min:0.1',
        ]);

        Tramo::create($data);

        return redirect()->route('tramos.index')
            ->with('success', 'Tramo registrado correctamente.');
    }

    public function update(Request $request, Tramo $tramo)
    {
        $data = $request->validate([
            'nombre'  => 'required|string|max:150',
            'origen'  => 'required|string|max:150',
            'destino' => 'required|string|max:150',
            'km'      => 'required|numeric|min:0.1',
        ]);

        $tramo->update($data);

        return redirect()->route('tramos.index')
            ->with('success', 'Tramo actualizado correctamente.');
    }

    public function destroy(Tramo $tramo)
    {
        $tramo->delete();
        return redirect()->route('tramos.index')
            ->with('success', 'Tramo eliminado.');
    }
}
