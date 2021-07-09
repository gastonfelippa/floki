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
                          'estado_pago', 'forma_de_pago', 'mercadopago', 'nro_comp_pago', 'estado_entrega', 
                          'user_id_delete', 'comentario', 'comercio_id', 'arqueo_id'];
}
