<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Detfactura extends Model
{
    use SoftDeletes; 
    
    protected $dates = ['deleted_at'];
    protected $table = 'detfacturas';
    protected $fillable = ['id', 'factura_id', 'producto_id', 'cantidad', 'precio', 'comercio_id'];
}
