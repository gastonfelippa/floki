<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stock';
    protected $fillable = ['stock_actual', 'stock_ideal', 'stock_minimo', 'producto_id', 
                            'comercio_id'];
}
