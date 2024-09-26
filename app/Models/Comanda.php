<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comanda extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'comandas';
    protected $fillable = ['factura_id', 'estado', 'sectorcomanda_id', 'sent_at', 'finished_at',
                           'comercio_id'];
}
