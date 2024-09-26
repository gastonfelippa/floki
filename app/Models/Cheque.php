<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cheque extends Model
{
    use SoftDeletes; 
    
    protected $dates = ['deleted_at'];    
    protected $table = 'cheques';
    protected $fillable = ['cliente_id', 'banco_id', 'numero' , 'fecha_de_emision',
        'fecha_de_pago', 'importe', 'cuit_titular', 'estado', 'salida', 'comercio_id'];
}
