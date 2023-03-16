<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    protected $table = 'balances';
    protected $fillable = ['existencia_inicial', 'compras_mercaderia', 'existencia_final', 'comercio_id'];
}
