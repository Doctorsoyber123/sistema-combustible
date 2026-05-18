<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

/**
 * Ejecuta el seeder solo si la base de datos esta vacia.
 *
 * Pensado para deploys automaticos en Render/Heroku/etc donde se llama
 * en cada arranque pero no debe duplicar datos.
 *
 * Uso: php artisan app:seed-if-empty
 */
class SeedIfEmpty extends Command
{
    protected $signature = 'app:seed-if-empty';

    protected $description = 'Ejecuta db:seed solo si no hay usuarios todavia';

    public function handle(): int
    {
        try {
            $count = User::count();
        } catch (\Throwable $e) {
            $this->warn('No se pudo consultar usuarios (¿migraciones pendientes?): ' . $e->getMessage());
            return self::FAILURE;
        }

        if ($count > 0) {
            $this->info(">> Base de datos con {$count} usuarios: omitiendo seed.");
            return self::SUCCESS;
        }

        $this->info('>> Base de datos vacia: ejecutando seed inicial...');
        $this->call('db:seed', ['--force' => true]);
        $this->info('>> Seed inicial completado.');

        return self::SUCCESS;
    }
}
