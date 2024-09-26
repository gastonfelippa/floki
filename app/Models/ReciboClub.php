<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReciboClub extends Model
{
    protected $table = 'recibo_clubs';
    protected $fillable = ['numero', 'importe', 'forma_de_pago', 'nro_comp_pago', 'mercadopago',
                           'comentario', 'entrega', 'socio_id', 'user_id', 'comercio_id', 'arqueo_id'];
}
