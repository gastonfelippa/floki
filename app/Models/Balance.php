<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    protected $table = 'caja_inicials';
    protected $fillable = ['existencia_inicial', 'compras_mercaderia', 'existencia_final'];
}
