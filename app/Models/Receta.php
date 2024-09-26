<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receta extends Model
{
    use SoftDeletes; 
    
    protected $dates = ['deleted_at'];
    protected $table = 'recetas';
    
    protected $fillable = ['producto_id', 'salsa_id', 'porciones', 'procedimiento', 
                            'comentario', 'comercio_id'];
}
