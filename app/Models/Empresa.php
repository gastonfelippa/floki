<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $fillable = ['nombre', 'telefono', 'email', 'direccion', 'logo'];

    protected $table = 'empresas';
}
