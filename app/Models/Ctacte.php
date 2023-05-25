<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ctacte extends Model
{
    protected $table = 'cta_cte';
    protected $fillable = ['cliente_id', 'factura_id', 'recibo_id', 'estado'];
}
