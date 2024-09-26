<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categoria extends Model
{
    use SoftDeletes; 
    
    protected $dates = ['deleted_at'];
    
    protected $table = 'categorias';
    protected $fillable = ['descripcion', 'margen_1', 'margen_2', 
                            'tipo_id', 'rubro_id', 'comercio_id'];
}
