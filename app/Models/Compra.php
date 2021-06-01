<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Compra extends Model
{
    use SoftDeletes; 
    
    protected $dates = ['deleted_at'];
    protected $table = 'compras';
    protected $fillable = ['letra', 'sucursal', 'num_fact', 'proveedor_id', 'user_id', 
                            'importe', 'estado', 'comercio_id'];
}
