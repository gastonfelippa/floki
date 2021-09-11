<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Remito extends Model
{
    use HasFactory;
    use SoftDeletes; 
    
    protected $dates = ['deleted_at'];

    protected $table = 'remitos';
    protected $fillable = ['numero', 'cliente_id', 'repartidor_id', 'user_id', 'estado', 'comercio_id'];
}
