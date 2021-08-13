<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Detcompra extends Model
{
    use SoftDeletes; 
    
    protected $dates = ['deleted_at'];
    protected $table = 'det_compras';
    protected $fillable = ['compra_id', 'producto_id', 'cantidad', 'precio', 'comercio_id'];

}
