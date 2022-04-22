<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receta extends Model
{
    use SoftDeletes; 
    
    protected $dates = ['deleted_at'];
    protected $table = 'recetas';
    protected $fillable = ['producto_receta_id', 'cantidad', 'unidad_de_medida', 'producto_id', 
                           'subproducto_id', 'comercio_id'];
}
