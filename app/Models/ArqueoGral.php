<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArqueoGral extends Model
{
    use HasFactory;

    protected $table = 'arqueo_grals';
    protected $fillable = ['estado', 'comercio_id'];
}
