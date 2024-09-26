<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Factura extends Model
{
    use SoftDeletes; 
    
    protected $dates = ['deleted_at'];
    protected $table = 'facturas';
    protected $fillable = ['numero', 'cliente_id', 'repartidor_id', 'user_id', 'importe', 'estado',  
                          'estado_pago', 'estado_entrega', 'lista', 'mesa_id', 'mozo_id', 'impresion',
                          'comentario', 'arqueo_id', 'comercio_id'];
}
