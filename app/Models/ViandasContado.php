<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViandasContado extends Model
{
    use HasFactory;

    protected $table = 'viandas_contados';
    protected $fillable = ['factura_id', 'cliente_id', 'arqueo_gral_id', 'estado'];  
}
