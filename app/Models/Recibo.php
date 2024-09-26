<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recibo extends Model
{
    protected $table = 'recibos';
    protected $fillable = ['numero', 'comentario', 'entrega', 'cliente_id', 'user_id', 'comercio_id', 'arqueo_id'];
}
