<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detpedido extends Model
{
    
    protected $table = 'detpedidos';
    protected $fillable = ['pedido_id', 'producto_id', 'cantidad', 'comercio_id'];
}
