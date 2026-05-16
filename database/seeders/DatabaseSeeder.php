<?php

namespace Database\Seeders;

use App\Models\Boleta;
use App\Models\Consumo;
use App\Models\TipoVehiculo;
use App\Models\Tramo;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Carga datos de ejemplo para FuelControl.
     */
    public function run(): void
    {
        // ── Usuarios ──
        // El cast 'hashed' del modelo User cifra las contraseñas automaticamente.
        // El acceso es por usuario + contraseña; el correo es solo interno.
        User::create([
            'name'     => 'Administrador',
            'username' => 'admin',
            'email'    => 'admin@fuelcontrol.local',
            'password' => 'admin',
            'role'     => 'admin',
            'activo'   => true,
        ]);

        User::create([
            'name'     => 'Carlos Quispe',
            'username' => 'trabajador',
            'email'    => 'trabajador@fuelcontrol.local',
            'password' => 'trabajador',
            'role'     => 'trabajador',
            'activo'   => true,
        ]);

        // ── Tipos de vehiculo (catalogo editable desde la pagina de Vehiculos) ──
        foreach (['Retroexcavadora', 'Camión', 'Volquete', 'Cargador frontal', 'Motoniveladora', 'Otro'] as $tipo) {
            TipoVehiculo::firstOrCreate(['nombre' => $tipo]);
        }

        // ── Vehiculos ──
        $vehiculos = collect([
            ['codigo' => 'RX-01',  'tipo' => 'Retroexcavadora', 'placa' => 'AHY-310', 'modelo' => 'CAT 320'],
            ['codigo' => 'RX-02',  'tipo' => 'Retroexcavadora', 'placa' => 'AHY-311', 'modelo' => 'CAT 320'],
            ['codigo' => 'CAM-01', 'tipo' => 'Camión',          'placa' => 'BKP-412', 'modelo' => 'Volvo FMX'],
            ['codigo' => 'CAM-02', 'tipo' => 'Camión',          'placa' => 'BKP-413', 'modelo' => 'Volvo FMX'],
        ])->map(fn ($v) => Vehiculo::create($v));

        // ── Tramos ──
        $tramos = collect([
            ['nombre' => 'Cantera - Planta', 'origen' => 'Cantera principal', 'destino' => 'Planta procesadora', 'km' => 12.5],
            ['nombre' => 'Almacén - Obra',   'origen' => 'Almacén central',   'destino' => 'Obra norte',         'km' => 8.3],
        ])->map(fn ($t) => Tramo::create($t));

        // ── Consumos ──
        $consumos = [
            ['v' => 0, 't' => 0, 'galones' => 50, 'fecha' => '2025-05-12', 'operador' => 'Carlos Quispe'],
            ['v' => 2, 't' => 1, 'galones' => 80, 'fecha' => '2025-05-13', 'operador' => 'Miguel Torres'],
            ['v' => 1, 't' => 0, 'galones' => 45, 'fecha' => '2025-05-14', 'operador' => 'Jorge Llanos'],
            ['v' => 3, 't' => 1, 'galones' => 60, 'fecha' => '2025-05-14', 'operador' => 'Luis Cano'],
        ];
        foreach ($consumos as $c) {
            Consumo::create([
                'vehiculo_id' => $vehiculos[$c['v']]->id,
                'tramo_id'    => $tramos[$c['t']]->id,
                'galones'     => $c['galones'],
                'fecha'       => $c['fecha'],
                'operador'    => $c['operador'],
            ]);
        }

        // ── Boletas ──
        $boletas = [
            ['num' => 'B-00230', 'fecha' => '2025-05-10', 'v' => 0, 'proveedor' => 'Primax Chimbote', 'galones' => 50, 'precio' => 7.20],
            ['num' => 'B-00231', 'fecha' => '2025-05-11', 'v' => 2, 'proveedor' => 'Repsol Norte',    'galones' => 80, 'precio' => 7.15],
            ['num' => 'B-00232', 'fecha' => '2025-05-13', 'v' => 1, 'proveedor' => 'Primax Chimbote', 'galones' => 45, 'precio' => 7.20],
        ];
        foreach ($boletas as $b) {
            Boleta::create([
                'numero_boleta' => $b['num'],
                'vehiculo_id'   => $vehiculos[$b['v']]->id,
                'proveedor'     => $b['proveedor'],
                'galones'       => $b['galones'],
                'precio_galon'  => $b['precio'],
                'total'         => round($b['galones'] * $b['precio'], 2),
                'fecha'         => $b['fecha'],
            ]);
        }
    }
}
