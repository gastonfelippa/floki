<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Socio extends Model
{
    use SoftDeletes; 
    
    protected $dates = ['deleted_at'];
    protected $table = 'socios';
    protected $fillable = ['tipo', 'categoria_id', 'nombre', 'apellido', 'calle', 'numero', 'localidad_id',
                           'telefono', 'email', 'documento', 'fecha_nac', 'fecha_alta', 'fecha_baja',
                           'cobrador_id', 'cobrar_en', 'comentario', 'grupo_familiar', 'estado', 'comercio_id'];
}
