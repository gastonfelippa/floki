<?php

namespace App\Traits;

trait PreciosTrait
{
    public function calcularPrecioVenta()
	{
        if($this->cambiar_precios <> 'no'){        
            $producto = Producto::find($this->producto);
            $categoria = $producto->categoria_id;
        //si es Art. Compra/Venta    
            $porcentaje = Categoria::where('id', $categoria)
                ->where('comercio_id', $this->comercioId)
                ->select('margen_1', 'margen_2')->get();

            if($this->cambiar_precios <> 'solo_costos'){  //modifica todo
                if ($this->calcular_precio_de_venta == 0){
                    //calcula el precio de venta sumando el margen de ganancia al costo del producto
                    $this->precio_venta_sug_l1 = ($this->precio * $porcentaje[0]->margen_1) / 100 + $this->precio;
                    $this->precio_venta_sug_l2 = ($this->precio * $porcentaje[0]->margen_2) / 100 + $this->precio;
                    $this->precio_venta_l1 = ($this->precio * $porcentaje[0]->margen_1) / 100 + $this->precio;
                    $this->precio_venta_l2 = ($this->precio * $porcentaje[0]->margen_2) / 100 + $this->precio;
                
                }else{
                    //calcula el precio de venta obteniendo el margen de ganancia sobre el mismo
                    $this->precio_venta_sug_l1 = $this->precio * 100 / (100 - $porcentaje[0]->margen_1);
                    $this->precio_venta_sug_l2 = $this->precio * 100 / (100 - $porcentaje[0]->margen_2);
                    $this->precio_venta_l1 = $this->precio * 100 / (100 - $porcentaje[0]->margen_1);
                    $this->precio_venta_l2 = $this->precio * 100 / (100 - $porcentaje[0]->margen_2);
                }
                if ($this->redondear_precio_de_venta == 1){
                    $this->precio_venta_sug_l1 = round($this->precio_venta_sug_l1);
                    $this->precio_venta_sug_l2 = round($this->precio_venta_sug_l2);
                    $this->precio_venta_l1 = round($this->precio_venta_l1);
                    $this->precio_venta_l2 = round($this->precio_venta_l2);
                }
            }else{           //modifica solo los precios de venta sugeridos
                if ($this->calcular_precio_de_venta == 0){
                    //calcula el precio de venta sumando el margen de ganancia al costo del producto
                    $this->precio_venta_sug_l1 = ($this->precio * $porcentaje[0]->margen_1) / 100 + $this->precio;
                    $this->precio_venta_sug_l2 = ($this->precio * $porcentaje[0]->margen_2) / 100 + $this->precio;
                }else{
                    //calcula el precio de venta obteniendo el margen de ganancia sobre el mismo
                    $this->precio_venta_sug_l1 = $this->precio * 100 / (100 - $porcentaje[0]->margen_1);
                    $this->precio_venta_sug_l2 = $this->precio * 100 / (100 - $porcentaje[0]->margen_2);
                }
                if ($this->redondear_precio_de_venta == 1){
                    $this->precio_venta_sug_l1 = round($this->precio_venta_sug_l1);
                    $this->precio_venta_sug_l2 = round($this->precio_venta_sug_l2);
                }
            }
        }
        $this->StoreOrUpdateButton(0);
	}	

}