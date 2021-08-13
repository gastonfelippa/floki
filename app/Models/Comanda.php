<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comanda extends Model
{
    use HasFactory;

    protected $table = 'comandas';
    protected $fillable = ['factura_id', 'estado', 'sectorcomanda_id', 'finished_at'];
}
