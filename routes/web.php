<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoletaController;
use App\Http\Controllers\ConsumoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TipoVehiculoController;
use App\Http\Controllers\TramoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VehiculoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - FuelControl
|--------------------------------------------------------------------------
*/

// ── Autenticacion ──
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Rutas protegidas (requieren sesion iniciada) ──
Route::middleware('auth')->group(function () {

    // Accesible para todos los usuarios (admin y trabajador)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('consumos', ConsumoController::class)
        ->only(['index', 'create', 'store', 'update', 'destroy']);

    Route::resource('boletas', BoletaController::class)
        ->only(['index', 'create', 'store', 'update', 'destroy']);

    // ── Solo administradores ──
    Route::middleware('admin')->group(function () {

        // Maestros
        Route::resource('vehiculos', VehiculoController::class)
            ->only(['index', 'create', 'store', 'update', 'destroy']);

        // Tipos de vehiculo (se gestionan desde la pagina de Vehiculos)
        Route::post('/tipos-vehiculo', [TipoVehiculoController::class, 'store'])->name('tipos-vehiculo.store');
        Route::delete('/tipos-vehiculo/{tipoVehiculo}', [TipoVehiculoController::class, 'destroy'])->name('tipos-vehiculo.destroy');

        Route::resource('tramos', TramoController::class)
            ->only(['index', 'create', 'store', 'update', 'destroy']);

        // Trabajadores / usuarios del sistema
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');

        // Reportes
        Route::get('/reportes/vehiculos', [DashboardController::class, 'reporteVehiculos'])
            ->name('reportes.vehiculos');
        Route::get('/reportes/tramos', [DashboardController::class, 'reporteTramos'])
            ->name('reportes.tramos');
    });
});
