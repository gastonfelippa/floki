<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReciboDebito extends Model
{
    protected $table = 'recibo_debitos';
    protected $fillable = ['recibo_club_id', 'debito_id'];
}
