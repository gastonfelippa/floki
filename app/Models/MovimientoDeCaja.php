<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovimientoDeCaja extends Model
{
    use HasFactory;
    protected $dates = ['deleted_at'];

    protected $table = 'movimiento_de_cajas';
    protected $fillable = ['ingreso_id', 'egreso_id', 'importe', 'user_id', 'comercio_id', 'arqueo_id'];
}
