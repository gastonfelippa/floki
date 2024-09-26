<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historico extends Model
{
    use HasFactory;
    protected $table = 'historicos';
    protected $fillable = ['producto_id', 'precio_costo', 'precio_venta_l1', 'precio_venta_l2', 
        'precio_venta_sug_l1', 'precio_venta_sug_l2', 'comercio_id'];
}
