<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categoria extends Model
{
    use SoftDeletes; 
    
    protected $dates = ['deleted_at'];
    
    protected $table = 'categorias';
    protected $fillable = ['descripcion', 'tipo', 'margen_1', 'margen_2', 'rubro_id',
                            'mostrar_al_vender', 'comercio_id'];
}
