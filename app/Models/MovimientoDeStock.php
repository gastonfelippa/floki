<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoDeStock extends Model
{
    use HasFactory;

    protected $table = 'movimiento_de_stock';
    protected $fillable = ['descripcion'];
}
