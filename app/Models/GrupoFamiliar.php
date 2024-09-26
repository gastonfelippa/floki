<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GrupoFamiliar extends Model
{
    use SoftDeletes; 
    
    protected $dates = ['deleted_at'];
    protected $table = 'grupo_familiar';
    protected $fillable = ['nombre', 'apellido', 'calle', 'numero', 'localidad_id', 'telefono',
                            'email', 'documento', 'fecha_nac', 'fecha_alta', 'fecha_baja',
                            'socio_id', 'estado','comercio_id'];
}
