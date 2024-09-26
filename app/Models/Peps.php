<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Peps extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'peps';
    protected $fillable = ['mov_stock_id', 'det_compra_id', 'det_venta_id', 'producto_id', 'cantidad', 
                           'resto', 'costo_historico', 'prod_modif_id', 'cant_prod_modif','user_id', 'comercio_id'];

}
