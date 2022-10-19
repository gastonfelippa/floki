<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reserva extends Model
{
    use HasFactory;
    use SoftDeletes; 
    
    protected $dates = ['deleted_at'];
    protected $table = 'reservas';
    protected $fillable = ['nombre', 'apellido', 'telefono', 'mesa_id', 'cantidad', 'hora', 
                           'comentario', 'comercio_id'];
}
