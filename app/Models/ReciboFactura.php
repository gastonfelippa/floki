<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReciboFactura extends Model
{
    use HasFactory;
    protected $table = 'recibo_facturas';
    protected $fillable = ['recibo_id', 'factura_id'];
}
