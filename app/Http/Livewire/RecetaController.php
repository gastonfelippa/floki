<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Auditoria;
use App\Models\Categoria;
use App\Models\Comercio;
use App\Models\Producto;
use App\Models\Receta;
use DB;
use Illuminate\Http\Request;


class RecetaController extends Component
{
    public $comercioId, $id_prod_receta;
    public $cantidad = null, $total, $comentario, $action = 1, $selected_id = null;
    public $unidad = 'Elegir', $producto = 'Elegir', $productos, $prod_receta, $prod = 'Helado 1 Bocha';
    public $porcentaje, $calcular_precio_de_venta, $redondear_precio_de_venta, $precio_venta_l1, $precio_venta_l2, $categoria;
    public $porcentaje_margen_1, $porcentaje_margen_2, $importe_anterior;

    public function render()
    {
        $this->comercioId = session('idComercio');
        $this->id_prod_receta = session('producto_receta_id');
//dd($this->id_prod_receta);
        $record = Comercio::find($this->comercioId);
		$this->calcular_precio_de_venta = $record->calcular_precio_de_venta;
		$this->redondear_precio_de_venta = $record->redondear_precio_de_venta;

        $producto = Producto::find($this->id_prod_receta);
        $this->prod_receta = $producto->descripcion;
        $this->categoria = $producto->categoria_id;
        $porcentaje = Categoria::where('id', $this->categoria)->select('margen_1', 'margen_2')->get();
        $this->porcentaje_margen_1 = $porcentaje[0]->margen_1;
        $this->porcentaje_margen_2 = $porcentaje[0]->margen_2;
//dd($this->porcentaje_margen_1 ,$this->pocentaje_margen_2);
        $this->productos = Producto::where('comercio_id', $this->comercioId)
                            ->where('tipo', 'not like', 'Art. Venta')->orderBy('descripcion')->get();

        $info = Receta::select('recetas.*', DB::RAW("'' as descripcion"), DB::RAW("'' as precio_costo"), DB::RAW("'' as importe"))
                        ->where('producto_receta_id', $this->id_prod_receta)
                        ->where('comercio_id', $this->comercioId)->get();
        $this->total = 0;                
        foreach ($info as $i){
            if($i->producto_id){
                $producto = Producto::find($i->producto_id);
                $i->descripcion = $producto->descripcion;
                $i->precio_costo = $producto->precio_costo;
                $i->importe = $i->cantidad * $producto->precio_costo;
            }
            $this->total = $this->total + $i->importe;
        }
        return view('livewire.recetas.component', ['info' => $info]);
    }
    public function doAction($action)
    {
        $this->action = $action;
        $this->resetInput();
    }
    public function resetInput(){
        $this->cantidad = null;
        $this->unidad = 'Elegir';
        $this->producto = 'Elegir';
        $this->action = 1;
        $this->selected_id = null;

    }
    protected $listeners = [
        'deleteRow' => 'destroy'
    ];
    public function edit($id)
    {
        $record = Receta::findOrFail($id);
        $this->selected_id = $id;
        $this->cantidad = $record->cantidad;
        $this->unidad = $record->unidad_de_medida;
        $this->producto = $record->producto_id;
        //calculo el importe del item a editar de la receta para luego descontar del total 
        $producto = Producto::find($this->producto);
        $precio_costo = $producto->precio_costo;
        $this->importe_anterior = $this->cantidad * $precio_costo;
    }
    public function StoreOrUpdate()
    { 
        $this->validate([
            'cantidad' => 'required|numeric|min:0|not_in:0',
            'unidad' => 'not_in:Elegir|required',
            'producto' => 'not_in:Elegir|required'
        ]);

        //calculo variables para luego actualizar precio de costo y venta del producto 
        $producto = Producto::find($this->producto);
        $cantidad = $this->cantidad;
        $precio_costo = $producto->precio_costo;
        $importe = $cantidad * $precio_costo;
        if($this->selected_id) $precio_costo = $this->total - $this->importe_anterior + $importe;
        else $precio_costo = $this->total + $importe;
        
        if ($this->calcular_precio_de_venta == 0){
            //calcula el precio de venta sumando el margen de ganancia al costo del producto
            $this->precio_venta_l1 = ($precio_costo * $this->porcentaje_margen_1) / 100 + $precio_costo;
            $this->precio_venta_l2 = ($precio_costo * $this->porcentaje_margen_2) / 100 + $precio_costo;
        }else{
            //calcula el precio de venta obteniendo el margen de ganancia sobre el mismo
            $this->precio_venta_l1 = $precio_costo * 100 / (100 - $this->porcentaje_margen_1);
            $this->precio_venta_l2 = $precio_costo * 100 / (100 - $this->porcentaje_margen_2);
        }
        if ($this->redondear_precio_de_venta == 1){
            $this->precio_venta_l1 = round($this->precio_venta_l1, 0);
            $this->precio_venta_l2 = round($this->precio_venta_l2, 0);
        }
        ////////////////
        DB::begintransaction();
        try{
            if($this->selected_id){
                $receta = Receta::find($this->selected_id);
                $receta->update([
                    'cantidad' => $this->cantidad, 
                    'unidad_de_medida' => $this->unidad, 
                    'producto_id' => $this->producto, 
                    'subproducto_id' => null,
                ]); 
            }else{
                $receta =  Receta::create([
                    'producto_receta_id' => $this->id_prod_receta, 
                    'cantidad' => $this->cantidad, 
                    'unidad_de_medida' => $this->unidad, 
                    'producto_id' => $this->producto, 
                    'subproducto_id' => null, 
                    'comercio_id' => $this->comercioId            
                ]);
            }
            $producto = Producto::find($this->id_prod_receta);
            $producto->update([
                'precio_costo' => $precio_costo,
                'precio_venta_l1' => $this->precio_venta_l1,
                'precio_venta_l2' => $this->precio_venta_l2
            ]);       
       
            if($this->selected_id) session()->flash('msg-ok', 'Item Actualizado');            
            else session()->flash('msg-ok', 'Item Creado'); 
            DB::commit(); 
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        $this->resetInput();
        return;
    }

    public function destroy($id)
    {
        if ($id) {
            DB::begintransaction();
            try{
                //calculo variables para luego actualizar precios de costo y venta del producto 
                $receta = Receta::find($id);
                $cantidad = $receta->cantidad;
                $producto = $receta->producto_id;
                $producto = Producto::find($producto);
                $precio_costo = $producto->precio_costo;
                $importe = $cantidad * $precio_costo;
                $precio_costo = $this->total - $importe;
                if ($this->calcular_precio_de_venta == 0){
                    //calcula el precio de venta sumando el margen de ganancia al costo del producto
                    $this->precio_venta_l1 = ($precio_costo * $this->porcentaje_margen_1) / 100 + $precio_costo;
                    $this->precio_venta_l2 = ($precio_costo * $this->porcentaje_margen_2) / 100 + $precio_costo;
                }else{
                    //calcula el precio de venta obteniendo el margen de ganancia sobre el mismo
                    $this->precio_venta_l1 = $precio_costo * 100 / (100 - $this->porcentaje_margen_1);
                    $this->precio_venta_l2 = $precio_costo * 100 / (100 - $this->porcentaje_margen_2);
                }
                if ($this->redondear_precio_de_venta == 1){
                    $this->precio_venta_l1 = number_format(round($this->precio_venta_l1, 0),2);
                    $this->precio_venta_l2 = number_format(round($this->precio_venta_l2, 0),2);
                }else{
                    $this->precio_venta_l1 = number_format($this->precio_venta_l1,2);
                    $this->precio_venta_l2 = number_format($this->precio_venta_l2,2);
                }
                //actualizo precios
                $producto = Producto::find($this->id_prod_receta);
                $producto->update([
                    'precio_costo' => $precio_costo,
                    'precio_venta_l1' => $this->precio_venta_l1,
                    'precio_venta_l2' => $this->precio_venta_l2
                ]);
                //elimino item de la receta
                $receta = Receta::find($id)->delete(); 
                $audit = Auditoria::create([
                    'item_deleted_id' => $id,
                    'tabla' => 'Recetas',
                    'estado' => '0',
                    'user_delete_id' => auth()->user()->id,
                    'comentario' => $this->comentario,
                    'comercio_id' => $this->comercioId
                ]);
                session()->flash('msg-ok', 'Registro eliminado con éxito!!');
                DB::commit();               
            }catch (Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se eliminó...');
            }
            $this->resetInput();
            return;
        }
    }
}
