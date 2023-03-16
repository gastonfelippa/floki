<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CajaInicial extends Model
{
    use SoftDeletes; 
    
    protected $dates = ['deleted_at'];
    
    protected $table = 'caja_inicials';
    protected $fillable = ['caja_user_id', 'tipo', 'cheque_id', 'importe', 'user_id'];
}
