<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class CtaCteClub extends Model
{
    protected $table = 'cta_cte_clubs';
    protected $fillable = ['socio_id', 'debito_id', 'recibo_id'];
}
