<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use SoftDeletes; 
    
    protected $dates = ['deleted_at'];
    protected $table = 'productos';
    protected $fillable = ['codigo','descripcion', 'precio_costo', 'precio_venta_l1', 'precio_venta_l2', 
                            'precio_venta_sug_l1', 'precio_venta_sug_l2', 'stock_ideal', 'stock_minimo', 
                            'estado', 'merma', 'presentacion', 'unidad_de_medida','tiene_receta',                            
                            'controlar_stock', 'categoria_id', 'proveedor_id', 'comercio_id',
                            'salsa', 'guarnicion', 'sectorcomanda_id', 'texto_base_comanda_id'];
}
