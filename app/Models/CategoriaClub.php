<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoriaClub extends Model
{
    use SoftDeletes; 
    
    protected $dates = ['deleted_at'];
    
    protected $table = 'categoria_club';
    protected $fillable = ['descripcion', 'edad_minima', 'edad_maxima', 'importe', 'comercio_id'];
}
