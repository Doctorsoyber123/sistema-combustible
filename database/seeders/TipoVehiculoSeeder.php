<?php

namespace Database\Seeders;

use App\Models\TipoVehiculo;
use Illuminate\Database\Seeder;

class TipoVehiculoSeeder extends Seeder
{
    /**
     * Tipos de vehiculo por defecto. Idempotente: no duplica si ya existen.
     */
    public function run(): void
    {
        $tipos = ['Retroexcavadora', 'Camión', 'Volquete', 'Cargador frontal', 'Motoniveladora', 'Otro'];

        foreach ($tipos as $nombre) {
            TipoVehiculo::firstOrCreate(['nombre' => $nombre]);
        }
    }
}
