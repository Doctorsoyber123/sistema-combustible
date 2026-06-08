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

        // ── Tramos: se mantienen dos tramos de ejemplo que usan otros seeders,
        // luego agregamos varias rutas reales parseadas desde una lista.
        $tramos = collect([
            ['nombre' => 'Cantera - Planta', 'origen' => 'Cantera principal', 'destino' => 'Planta procesadora', 'km' => 12.5],
            ['nombre' => 'Almacén - Obra',   'origen' => 'Almacén central',   'destino' => 'Obra norte',         'km' => 8.3],
        ])->map(fn ($t) => Tramo::create($t));

        // Lista de rutas reales (codigo, turno, frecuencia, descripcion)
        $routes = [
            ['codigo' => 'RC-LUN-MIE-VIE/MAÑANA-001', 'turno' => 'Mañana', 'frecuencia' => 'Lunes, Miércoles y Viernes', 'descripcion' => 'P. J PPAO, Urb. Los Álamos, P.J David Dasso 1° Y 2° etapa, A.H Portales del norte, A.H 28 de marzo, A.H. Fortaleza, A.H Miraflores de la paz, Laderas del PPAO'],
            ['codigo' => 'RC- LUN-MIE-VIE/MAÑANA-002', 'turno' => 'Mañana', 'frecuencia' => 'Lunes, Miércoles y Viernes', 'descripcion' => 'P.J 01 de Mayo, Colegio Cesar Vallejo, P.J 03 de Octubre parte baja, Ampliación 03 de Octubre, P.J 03 de Octubre parte alta, Colegio Fe Alegría'],
            ['codigo' => 'RC- LUN-MIE-VIE/MAÑANA-003', 'turno' => 'Mañana', 'frecuencia' => 'Lunes, Miércoles y Viernes', 'descripcion' => 'Urb. Mariscal Luzuriaga, AH Los Jardines, Asoc. La Ladrillera, Urb. Pacifico, Urb. Los Portales, Urb. Los Cipreses, Urb. El Bosque, Urb. Los Álamos, Urb. Santa Rosa'],
            ['codigo' => 'RC- LUN-MIE-VIE/MAÑANA-004', 'turno' => 'Mañana', 'frecuencia' => 'Lunes, Miércoles y Viernes', 'descripcion' => 'Urb. Casuarinas I y II Etapa, Urb. Bruces'],
            ['codigo' => 'RC- LUN-MIE-VIE/MAÑANA-005', 'turno' => 'Mañana', 'frecuencia' => 'Lunes, Miércoles y Viernes', 'descripcion' => 'Urb. Banchero Rosi, Urb. Santa Cristina, Urb. San Rafael, Urb. El Dorado, Las Gardenias, Urb. Santo Tomas, Urb. Villa Agraria, Urb. Bella mar 1° etapa'],
            ['codigo' => 'RC-7d/MAÑANA-001', 'turno' => 'Mañana', 'frecuencia' => 'Lunes a Domingo', 'descripcion' => 'Av. Central (Alta y Baja), Prolongación Pacifico, Alameda de San Luis, Av.03, Av. Integración, Av. Alcatraz, Panamericana Norte, Alameda el Dorado, Panamericana Sur, Entrada a la Playa Atahualpa, Entrada al AH. Brisas del Mar, Entrada de A.H. Villa Atahualpa, Av. La Paz, Av. Chinecas'],
            ['codigo' => 'RC-7d/MAÑANA-002', 'turno' => 'Mañana', 'frecuencia' => 'Lunes a Domingo', 'descripcion' => 'Urb. Cáceres Aramayo, Av. Pacífico, Jr. Pacifico, Av. Pardo, Av. Argentina, Av. Samanco, Av. Country, Av. Los Fresnos, Av. Las Palmeras, Av. Los Álamos, Av. Canalones, Av. San Fernando Centro Cívico, Mercado Buenos Aires, Av. Anchoveta, Av. Ugel Pedagógico, Plaza Mayor, Av. Las Gardenias, Av. Brasil, Av. Universitaria, Av. La Marina'],
            ['codigo' => 'RC-7d/MAÑANA-003', 'turno' => 'Mañana', 'frecuencia' => 'Lunes a Domingo', 'descripcion' => 'Av. 1° de Agosto, Almacén Sipesa, Av. La Marina, Av. Miraflores, Av. Perú, AH. Villa Marcela, Av. Fe y Alegría, Pj. Divino Jesús, Pj. Las Lomas, Av. Pelicano, Av. Brasil, Av. Universitaria, Av. Anchoveta, Av. Agraria, Av. Principal de la Urb. García Ronceros'],
            ['codigo' => 'RC- LUN-MIE-VIE/TARDE-001', 'turno' => 'Tarde', 'frecuencia' => 'Lunes, Miércoles y Viernes', 'descripcion' => 'A.H Bella Vista, A.H Houston II Etapa, PJ Villa Victoria, A.H Lomas del Sur, P.J Villa Don Víctor, P.J Praderas del Sur, A.H Sánchez Milla, Av. Agraria'],
            ['codigo' => 'RC- LUN-MIE-VIE/TARDE-002', 'turno' => 'Tarde', 'frecuencia' => 'Lunes, Miércoles y Viernes', 'descripcion' => 'Urb. Domus Hogares, Urb. Paseo del Mar, Urb. Villa de la Marina, A.H Geranios zona A, HUP. Belén, A.H Belén'],
            ['codigo' => 'RC- LUN-MIE-VIE/TARDE-003', 'turno' => 'Tarde', 'frecuencia' => 'Lunes, Miércoles y Viernes', 'descripcion' => 'A.H. María Idelsa, A.H Ríos Salcedo, A.H Joselyn de Álvarez, A.H La Planicie, A.H Los Ficus, A.H Juan Bautista, A.H Jesús María, P.J Vista al Mar 2, Av. Industrial, A.H Villa Express'],
            ['codigo' => 'RC- LUN-MIE-VIE/TARDE-004', 'turno' => 'Tarde', 'frecuencia' => 'Lunes, Miércoles y Viernes', 'descripcion' => 'A.H. Los Portales del Sol, AH San Francisco de Asís, A.H. Nuevo Paraíso, A.H Los Palmares, Ampliación Los Palmares, AH 14 de Noviembre, A.H Costa Blanca, AH Los Pinos del Sur, A.H Los Balcones, A.H Dunas del Sur, A.H Luis Arroyo'],
            ['codigo' => 'RC- LUN-MIE-VIE/TARDE-005', 'turno' => 'Tarde', 'frecuencia' => 'Lunes, Miércoles y Viernes', 'descripcion' => 'P.J El Amauta, Urb. El Periodista, P.J Los Jardines, P.J Toledo, P.J Villa del Mar, P.J 01 de agosto, P.J Villa Mercedes, P.J Víctor Raúl, AH El Salvador, HUP. Villa del Sur, P.J 1ra y 2da Etapa de San Luis'],
            ['codigo' => 'RC- LUN-MIE-VIE/TARDE-006', 'turno' => 'Tarde', 'frecuencia' => 'Lunes, Miércoles y Viernes', 'descripcion' => 'AH. Vista al Mar 1, AH. Tierra Prometida, AH. Casuarinas del Sur, AH. Roosworht, AH. Rosales del Mirador, AH. Río Santa, A.H Villa del Universitario'],
            ['codigo' => 'RC- LUN-MIE-VIE/TARDE-007', 'turno' => 'Tarde', 'frecuencia' => 'Lunes, Miércoles y Viernes', 'descripcion' => 'A.H. Segundo Ronceros, Ampliación de Garatea, Urb. Garatea I y II Etapa, A.H Los Geranios sector B'],
            ['codigo' => 'RC- LUN-MIE-VIE/TARDE-008', 'turno' => 'Tarde', 'frecuencia' => 'Lunes, Miércoles y Viernes', 'descripcion' => 'A.H Brisas del mar, A.H Villa Atahualpa, A.H Villa municipal'],
            ['codigo' => 'RC-7d/TARDE-001', 'turno' => 'Tarde', 'frecuencia' => 'Lunes a Domingo', 'descripcion' => 'Av. 1° de Agosto, Almacén Sipesa, Av. Pacifico, Jr. Pacifico, Av. Pardo, Av. San Fernando, Av. Country, Av. Los Fresnos, Av. Las Palmeras, Av. Los Álamos, Av. Canalones, Centro cívico, Mercado Buenos Aires, Av. Anchoveta, Av. Ugel Pedagógico, Av. Argentina, Plaza Mayor, Av. Las Gardenias, Av. Brasil, Av. Universitaria, La Marina'],
            ['codigo' => 'RC-MAR-JUE-SAB/MAÑANA-001', 'turno' => 'Mañana', 'frecuencia' => 'Martes, Jueves y Sábado', 'descripcion' => 'P.J Villa María, Av. Aviación, Ampliación Villa María'],
            ['codigo' => 'RC-MAR-JUE-SAB/MAÑANA-002', 'turno' => 'Mañana', 'frecuencia' => 'Martes, Jueves y Sábado', 'descripcion' => 'Urb. José Carlos Mariátegui, Pj. San Juan de los Álamos, Urb. Praderas de los Portales, Urb. Villa Sol de Chimbote, Urb. La Floresta, Urb. Los Héroes, Urb. Los Olivos'],
            ['codigo' => 'RC-7d/NOCHE-001', 'turno' => 'Noche', 'frecuencia' => 'Lunes a Domingo', 'descripcion' => 'Av. Pacifico, Av. Pardo, Jr. Pacifico, Av. Bruces, Av. Las palmeras, Av. Los Álamos, Doble vía entre la Urb. Bruces y Cáceres Aramayo, Av. Canalones, Av. San Fernando, Mercado Buenos Aires, Av. Anchoveta, Av. Chimbote, Av. Country, Av. La Marina'],
            ['codigo' => 'RC-7d/NOCHE-002', 'turno' => 'Noche', 'frecuencia' => 'Lunes a Domingo', 'descripcion' => 'Av. Ugel pedagógico, Av. Argentina, Av. Las gardenias, Av. Brasil, Av. Anchoveta media y alta, Av. Universitaria, Av. Anchoveta alta, Av. Naciones unidas, Av. Agraria baja, Av. Central'],
        ];

        $extraTramos = collect($routes)->map(function ($r) {
            // intentar extraer primer y último punto como origen/destino
            $parts = array_values(array_filter(array_map(fn($s) => trim($s), preg_split('/,|;|\\n/', $r['descripcion']))));
            $origen = $parts[0] ?? 'N/A';
            $destino = $parts[count($parts) - 1] ?? 'N/A';
            return Tramo::create([
                'nombre' => trim($r['codigo'] . ' - ' . $r['turno']),
                'origen' => $origen,
                'destino' => $destino,
                'km' => 0.00,
                'descripcion' => $r['descripcion'] ?? null,
            ]);
        });

        // combinar tramos ejemplo + tramos reales en una sola colección (mantiene índices)
        $tramos = $tramos->concat($extraTramos)->values();

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
