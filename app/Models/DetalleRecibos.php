<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetalleRecibos extends Model
{
    use HasFactory;
    use SoftDeletes; 
    
    protected $dates = ['deleted_at'];
    protected $table = 'detalle_recibos';
    protected $fillable = ['id', 'factura_id', 'recibo_id'];
}
