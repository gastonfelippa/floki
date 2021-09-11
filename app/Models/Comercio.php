<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comercio extends Model
{
    protected $table = 'comercios';
    protected $fillable = ['nombre', 'tipo_id', 'hora_apertura', 'telefono', 'email',
                           'direccion','logo', 'leyenda_factura', 'periodo_arqueo', 
                           'imp_por_hoja', 'imp_duplicado'];

}
