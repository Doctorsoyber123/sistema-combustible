<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Boleta extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_boleta', 'vehiculo_id', 'proveedor',
        'galones', 'precio_galon', 'total', 'fecha', 'evidencia',
    ];

    protected $casts = [
        'fecha'        => 'date',
        'galones'      => 'decimal:2',
        'precio_galon' => 'decimal:2',
        'total'        => 'decimal:2',
    ];

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function consumos()
    {
        return $this->hasMany(Consumo::class);
    }
}
