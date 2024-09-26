<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    protected $table ='auditorias';

    protected $fillable = ['item_deleted_id', 'tabla', 'estado','comentario', 
                           'user_delete_id', 'comercio_id'];
}
