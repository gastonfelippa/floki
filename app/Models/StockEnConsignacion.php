<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockEnConsignacion extends Model
{
    use HasFactory;
    
    protected $table = 'stock_en_consignacion';
    protected $fillable = ['cliente_id', 'remito_id', 'factura_id', 'producto_id',
                           'cantidad', 'comercio_id'];
}
