<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Debito extends Model
{
    use HasFactory;

    protected $dates = ['deleted_at'];
    protected $table = 'debitos';
    protected $fillable = ['numero', 'socio_id', 'importe', 'estado', 'estado_pago', 'comercio_id'];
}
