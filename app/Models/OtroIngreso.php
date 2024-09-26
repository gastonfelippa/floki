<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtroIngreso extends Model
{
    use HasFactory;
    use SoftDeletes; 
    
    protected $dates = ['deleted_at'];

    protected $table = 'otro_ingresos';
    protected $fillable = ['descripcion', 'comercio_id'];
}
