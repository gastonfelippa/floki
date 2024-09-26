<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetRemito extends Model
{
    use SoftDeletes; 
    
    protected $dates = ['deleted_at'];
    protected $table = 'det_remitos';
    protected $fillable = ['remito_id', 'producto_id', 'cantidad', 'comercio_id'];
}
