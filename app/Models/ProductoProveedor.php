<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoProveedor extends Model
{
    use HasFactory;
    protected $table = 'producto_proveedores';
    protected $fillable = ['producto_id', 'proveedor_id', 'comercio_id'];
}
