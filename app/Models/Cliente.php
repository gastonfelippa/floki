<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use SoftDeletes; 
    
    protected $dates = ['deleted_at'];
    protected $table = 'clientes';
    protected $fillable = ['nombre', 'apellido', 'calle', 'numero', 'localidad_id',
                           'telefono', 'vianda', 'consignatario', 'saldo', 'comercio_id'];
}
