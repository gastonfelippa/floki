<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detfactura_sp extends Model
{
    use HasFactory;

    protected $table = 'detfacturas_sp';
    protected $fillable = ['factura_id', 'producto_id', 'subproducto_id', 'cantidad', 'comercio_id'];
}
