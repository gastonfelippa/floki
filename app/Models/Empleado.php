<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $table = 'empleados';
    protected $fillable = ['nombre', 'apellido', 'documento', 'calle', 'numero', 'localidad_id',
                           'telefono', 'fecha_ingreso', 'fecha_nac', 'comercio_id'];
}
