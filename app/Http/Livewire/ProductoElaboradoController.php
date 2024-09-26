<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Traits\GenericTrait;
use App\Models\Comercio;
use App\Models\DetReceta;
use App\Models\Peps;
use App\Models\Producto;
use DB;

class ProductoElaboradoController extends Component
{
    use GenericTrait;

    public $comercioId, $search, $venta_sin_stock_general;

    public function render()
    {
        $this->comercioId = session('idComercio');

        //verifico para luego preguntar o no al vender sin stock
        $comercio = Comercio::where('id', $this->comercioId)->select('venta_sin_stock')->first();
        $this->venta_sin_stock_general = $comercio->venta_sin_stock;
        
		if(strlen($this->search) > 0) {
            $info = Producto::join('categorias as c', 'c.id', 'productos.categoria_id')
                ->where('productos.descripcion', 'like', '%' . $this->search .'%')
                ->where('productos.comercio_id', $this->comercioId)
				->where('c.tipo_id', 3)
                ->orWhere('productos.descripcion', 'like', '%' . $this->search .'%')
                ->where('productos.comercio_id', $this->comercioId)
                ->where('c.tipo_id', 4)
                ->select('productos.id', 'productos.descripcion', 'productos.unidad_de_medida', DB::RAW("'' as stock"), DB::RAW("1 as edit"))
				->orderBy('productos.descripcion')
				->get();
		}else {
			$info = Producto::join('categorias as c', 'c.id', 'productos.categoria_id')
                ->where('productos.comercio_id', $this->comercioId)
                ->where('c.tipo_id', 3)
                ->orWhere('productos.comercio_id', $this->comercioId)
                ->where('c.tipo_id', 4)
                ->select('productos.id', 'productos.descripcion', 'productos.unidad_de_medida', DB::RAW("'' as stock"), DB::RAW("1 as edit"))
				->orderBy('productos.descripcion')
				->get();
		}
        if($info->count() > 0) {
            foreach ($info as $i) {
                $stock = Peps::where('producto_id', $i->id)->where('comercio_id', $this->comercioId)->sum('resto');
                $i->stock = $stock;
                $detalle = DetReceta::join('recetas as r', 'r.id', 'det_recetas.receta_id')
                    ->join('productos as p', 'p.id', 'det_recetas.producto_id')
                    ->join('categorias as c', 'c.id', 'p.categoria_id')
                    ->where('r.producto_id', $i->id)
                    ->select('det_recetas.producto_id', 'c.tipo_id')->get();
                foreach ($detalle as $j) {
                    if ($j->tipo_id == 4) {
                        $i->edit = 0;
                    }
                }
            }
        }
        return view('livewire.productos-elaborados.component', ['info' => $info]);
    }

    protected $listeners = [ 'actualizar' ];

    public function verificar_stock($id,$stock_nuevo)
    {
        $producto = Producto::find($id);

        $verificar_stock = $this->verificarStockTrait($stock_nuevo, $id);
       
        if ($verificar_stock[0] == 1) {
            $this->emit('receta_sin_principal', $producto->descripcion); 
            return;
        } elseif ($verificar_stock[0] == 2) {
            if ($this->venta_sin_stock_general == 1) {
                $this->emit('stock_receta_no_disponible_con_opcion',$verificar_stock[1], $verificar_stock[2]);
            } else $this->emit('stock_receta_no_disponible_sin_opcion',$verificar_stock[1], $verificar_stock[2]);
            return;
        } elseif ($verificar_stock[0] == 3) {
            $this->emit('receta_sin_detalle', $producto->descripcion);
            return; 
        } elseif ($verificar_stock[0] == 4) { //stock no disponible CON opción
            $peps = Peps::where('producto_id', $id)->where('comercio_id', $this->comercioId)->sum('resto');
            if ($this->venta_sin_stock_general == 1) {
                $this->emit('stock_no_disponible_con_opcion', $peps, $producto->descripcion, $producto->id);
            } else $this->emit('stock_no_disponible_sin_opcion', $peps, $producto->descripcion);
            return;
        } elseif ($verificar_stock[0] == 5) { //stock no disponible SIN opción
            $peps = Peps::where('producto_id', $id)->where('comercio_id', $this->comercioId)->sum('resto');
            $this->emit('stock_no_disponible_sin_opcion', $peps, $producto->descripcion);
            return; 
        // } elseif ($verificar_stock[0] == 4) { //stock no disponible CON opción
        //     $peps = Peps::where('producto_id', $id)->where('comercio_id', $this->comercioId)->sum('resto');
        //     if ($this->venta_sin_stock_general == 1) {
        //         $this->emit('stock_no_disponible_con_opcion', $peps, $producto->descripcion, $producto->id);
        //     } else $this->emit('stock_no_disponible_sin_opcion', $peps, $producto->descripcion);
        //     return;
        // } elseif ($verificar_stock[0] == 5) { //stock no disponible SIN opción
        //     $peps = Peps::where('producto_id', $id)->where('comercio_id', $this->comercioId)->sum('resto');
        //     if ($this->venta_sin_stock_general == 1) {
        //         $this->emit('stock_no_disponible_con_opcion', $peps, $producto->descripcion, $producto->id);
        //     } else $this->emit('stock_no_disponible_sin_opcion', $peps, $producto->descripcion);
        //     //$this->emit('stock_no_disponible_sin_opcion', $peps, $producto->descripcion);
        //     return; 
        } else return true;
    }

    public function actualizar($productoId, $stock_nuevo)
    {
        if ($stock_nuevo >= 0) {
            $actualizar = false;
            $producto = Producto::find($productoId);
            if ($producto) {
                if ($this->verificar_stock($productoId,$stock_nuevo)) { 
                $actualizar = $this->actualizarStockTrait(4, false, false, null, null, 
                        null, $productoId, $producto->precio_costo, $stock_nuevo);
                } else session()->flash('msg-error', 'El Stock no se pudo verificar...');	
            }
            if ($actualizar) session()->flash('msg-ok', 'El Stock se actualizó correctamente!');
            else session()->flash('msg-error', 'El Stock no se pudo actualizar...');
        } else session()->flash('msg-error', 'El Stock Nuevo no puede ser negativo!');
    }
}