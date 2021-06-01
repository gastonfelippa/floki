<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vianda extends Model
{
    protected $table = 'viandas';
    protected $fillable = ['cliente_id', 'producto_id', 'estado', 'comentarios', 
        'h_lunes_m', 'h_lunes_n','h_martes_m', 'h_martes_n', 'h_miercoles_m', 'h_miercoles_n',
        'h_jueves_m', 'h_jueves_n', 'h_viernes_m','h_viernes_n', 'h_sabado_m', 'h_sabado_n',
        'h_domingo_m', 'h_domingo_n', 'c_lunes_m', 'c_lunes_n','c_martes_m', 'c_martes_n',
        'c_miercoles_m', 'c_miercoles_n', 'c_jueves_m', 'c_jueves_n', 'c_viernes_m',
        'c_viernes_n', 'c_sabado_m', 'c_sabado_n','c_domingo_m', 'c_domingo_n'];   
}
