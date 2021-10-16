<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subproducto extends Model
{
    use HasFactory;
    use SoftDeletes; 

    protected $dates = ['deleted_at'];
    protected $table = 'subproductos';
    protected $fillable = ['producto_id', 'descripcion', 'stock', 'stock_minimo','comercio_id'];
}
