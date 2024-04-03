<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostoFijoEstimado extends Model
{
    use SoftDeletes; 
    
    protected $dates = ['deleted_at'];
    
    protected $table = 'costo_fijo_estimados';
    protected $fillable = ['descripcion', 'importe', 'comercio_id'];
}
