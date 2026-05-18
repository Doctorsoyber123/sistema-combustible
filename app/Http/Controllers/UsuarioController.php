<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $q    = $request->input('q');
        $role = $request->input('role');

        $usuarios = User::query()
            ->when($q, fn ($query) => $query->where(fn ($w) =>
                $w->whereRaw('LOWER(name) LIKE LOWER(?)', ["%{$q}%"])
                  ->orWhereRaw('LOWER(username) LIKE LOWER(?)', ["%{$q}%"])))
            ->when($role, fn ($query) => $query->where('role', $role))
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('usuarios.index', compact('usuarios', 'q', 'role'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:150',
            'username' => 'required|string|max:50|alpha_dash|unique:users,username',
            'password' => 'required|string|min:4|confirmed',
            'role'     => 'required|in:admin,trabajador',
        ]);

        $data['activo'] = true;
        // El correo es interno (no se usa para iniciar sesion); se genera a partir del usuario
        $data['email']  = $data['username'] . '@fuelcontrol.local';

        // El cast 'hashed' del modelo User cifra la contraseña automaticamente
        User::create($data);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function update(Request $request, User $usuario)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:150',
            'username' => 'required|string|max:50|alpha_dash|unique:users,username,' . $usuario->id,
            'password' => 'nullable|string|min:4|confirmed',
            'role'     => 'required|in:admin,trabajador',
            'activo'   => 'nullable|boolean',
        ]);

        // Si no se envia password (campo vacio), conservar la actual
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $data['activo'] = $request->boolean('activo', $usuario->activo);
        // Mantener el correo coherente con el usuario
        $data['email']  = $data['username'] . '@fuelcontrol.local';

        // Si el usuario se edita a si mismo, no permitir cambiarse el rol ni desactivarse
        if ($usuario->id === auth()->id()) {
            $data['role']   = $usuario->role;
            $data['activo'] = true;
        }

        $usuario->update($data);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado.');
    }
}
