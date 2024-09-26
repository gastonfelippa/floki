<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comercio extends Model
{
    protected $table = 'comercios';
    
    protected $fillable = [
        'nombre', 
        'tipo_id', 
        'hora_apertura', 
        'telefono', 
        'email', 
        'direccion',                        
        'logo', 
        'leyenda_factura', 
        'periodo_arqueo', 
        'venta_sin_stock', 'imp_por_hoja',                         
        'imp_duplicado', 
        'calcular_precio_de_venta', 
        'redondear_precio_de_venta',                         
        'opcion_de_guardado_compra', 
        'opcion_de_guardado_producto', 
        'localidad_id'
    ];
}
