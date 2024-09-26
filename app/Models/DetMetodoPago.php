<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class DetMetodoPago extends Model
{
    use HasFactory;
    use SoftDeletes; 
    
    protected $dates = ['deleted_at'];

    protected $table = 'det_metodo_pagos';
    protected $fillable = ['factura_id', 'recibo_id', 'medio_de_pago', 'num_comp_pago', 'importe',
                           'arqueo_id', 'comercio_id'];
}
