<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocioActividad extends Model
{
    use HasFactory;

    protected $table = 'socio_actividad';
    protected $fillable = ['socio_id', 'actividad_id'];
}
