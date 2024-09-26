<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rubro extends Model
{
    use SoftDeletes; 
    
    protected $dates = ['deleted_at'];
    
    protected $table = 'rubros';
    protected $fillable = ['descripcion', 'mostrar', 'comercio_id'];
}
