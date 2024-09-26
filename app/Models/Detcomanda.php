<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Detcomanda extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'detcomandas';
    protected $fillable = ['comanda_id', 'producto_id', 'cantidad', 'descripcion', 
                           'comercio_id'];
}
