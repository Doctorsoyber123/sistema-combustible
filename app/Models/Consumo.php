<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consumo extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehiculo_id', 'tramo_id', 'galones', 'fecha', 'operador', 'observaciones', 'boleta_id',
    ];

    protected $casts = [
        'fecha'   => 'date',
        'galones' => 'decimal:2',
    ];

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function tramo()
    {
        return $this->belongsTo(Tramo::class);
    }

    public function boleta()
    {
        return $this->belongsTo(Boleta::class);
    }
}
