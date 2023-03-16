<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetReceta extends Model
{
    use SoftDeletes; 
    
    protected $dates = ['deleted_at'];
    protected $table = 'det_recetas';

    protected $fillable = ['receta_id', 'cantidad', 'unidad_de_medida',
                            'producto_id', 'principal', 'comercio_id'];
}
