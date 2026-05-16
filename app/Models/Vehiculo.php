<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;

    protected $fillable = ['codigo', 'tipo', 'placa', 'modelo', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function consumos()
    {
        return $this->hasMany(Consumo::class);
    }

    public function boletas()
    {
        return $this->hasMany(Boleta::class);
    }
}
