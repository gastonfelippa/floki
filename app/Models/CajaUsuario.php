<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CajaUsuario extends Model
{
    use HasFactory;
    use SoftDeletes; 
    
    protected $dates = ['deleted_at'];
    
    protected $table = 'caja_usuarios';
    protected $fillable = ['caja_id', 'caja_usuario_id', 'estado', 'caja_final_sistema', 'diferencia', 'user_id', 'arqueo_gral_id'];
}
