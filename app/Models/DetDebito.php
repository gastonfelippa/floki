<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class DetDebito extends Model
{
    use HasFactory;

    protected $dates = ['deleted_at'];
    protected $table = 'det_debitos';
    protected $fillable = ['debito_id', 'debito_generado_id', 'actividad_id', 'importe'];
}
