<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tramo extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'origen', 'destino', 'km', 'activo', 'descripcion', 'turno', 'galones'];

    protected $casts = [
        'km'      => 'decimal:2',
        'activo'  => 'boolean',
        'galones' => 'decimal:2',
    ];

    public function consumos()
    {
        return $this->hasMany(Consumo::class);
    }
}
