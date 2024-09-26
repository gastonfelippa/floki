<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proceso extends Model
{
    protected $table = 'procesos';
    protected $fillable = ['descripcion', 'fecha_ejecucion'];
}
