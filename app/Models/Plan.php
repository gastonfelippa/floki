<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $table = 'planes';
    protected $fillable = ['descripcion', 'precio', 'duracion', 'estado'];
}
