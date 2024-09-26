<?php

namespace App\Imports;

use App\Models\Producto;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class ProductosImport implements ToModel, WithHeadingRow, WithUpserts
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // if (!isset($row['salsa'])) {    //si el valor pasado es null, 
        //     return null;                //retorna null para todo el registro y no lo graba
        // }
             
        return new Producto([
            'codigo'          => $row['codigo'],
            'descripcion'     => $row['descripcion'],
            'precio_venta_l1' => $row['precio_venta_l1'],
            'tipo'            => $row['tipo'],
            'tiene_receta'    => $row['tiene_receta'],
            'controlar_stock' => $row['controlar_stock'],
            'categoria_id'    => $row['categoria_id'],
            'salsa'           => $row['salsa'],
            'guarnicion'      => $row['guarnicion'],
            'comercio_id'     => $row['comercio_id']
        ]);
    }   
    public function uniqueBy()
    {
        return 'descripcion';
    }
}
