<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class DebitoGenerado extends Model
{
    use HasFactory;

    protected $dates = ['deleted_at'];
    protected $table = 'debitos_generados';
    protected $fillable = ['mes_año', 'user_id', 'comercio_id'];
}
