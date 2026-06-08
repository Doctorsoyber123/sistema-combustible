<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tramo extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'origen', 'destino', 'km', 'activo', 'descripcion', 'turno'];

    protected $casts = [
        'km'     => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function consumos()
    {
        return $this->hasMany(Consumo::class);
    }
}
