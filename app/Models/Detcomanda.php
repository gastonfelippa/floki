<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detcomanda extends Model
{
    use HasFactory;

    protected $table = 'detcomandas';
    protected $fillable = ['comanda_id', 'producto_id', 'subproducto_id','cantidad', 'descripcion', 'comercio_id'];
}
