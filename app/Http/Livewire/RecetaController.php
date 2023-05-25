<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Auditoria;
use App\Models\Categoria;
use App\Models\Comercio;
use App\Models\Detfactura;
use App\Models\DetReceta;
use App\Models\Factura;
use App\Models\Producto;
use App\Models\Receta;
use DB;
use Illuminate\Http\Request;


class RecetaController extends Component
{
    public $comercioId, $id_prod_receta;
    public $cantidad = null, $total, $comentario, $action = 1, $selected_id = null;
    public $unidad = 'Elegir', $producto = 'Elegir', $productos, $prod_receta, $prod = 'Helado 1 Bocha';
    public $porcentaje, $calcular_precio_de_venta, $redondear_precio_de_venta, $categoria;
    public $precioCosto, $precio_venta_sug_l1, $precio_venta_sug_l2, $precio_venta_l1, $precio_venta_l2;
    public $porcentaje_margen_1, $porcentaje_margen_2, $importe_anterior, $preguntarPorPrecio;
    public $recetaId, $tiene_receta, $procedimiento, $cambiar_precios, $detalleProductoCargado;

    public function render()
    {
        $this->comercioId = session('idComercio');
        $this->id_prod_receta = session('producto_receta_id');

        if(!$this->cambiar_precios) $this->cambiar_precios = 'solo_costos';
        if(!$this->preguntarPorPrecio) $this->preguntarPorPrecio = 'si';

        $record = Comercio::find($this->comercioId);
		$this->calcular_precio_de_venta = $record->calcular_precio_de_venta;
		$this->redondear_precio_de_venta = $record->redondear_precio_de_venta;

        $producto = Producto::find($this->id_prod_receta);
        $this->prod_receta = $producto->descripcion;
        $this->categoria = $producto->categoria_id;

        $porcentaje = Categoria::where('id', $this->categoria)->select('margen_1', 'margen_2')->get();
        $this->porcentaje_margen_1 = $porcentaje[0]->margen_1;
        $this->porcentaje_margen_2 = $porcentaje[0]->margen_2;

        $this->productos = Producto::where('comercio_id', $this->comercioId)
                            ->where('tipo', 'not like', 'Art. Venta')->orderBy('descripcion')->get();

        $this->tiene_receta = Receta::where('producto_id', $this->id_prod_receta)
            ->where('comercio_id', $this->comercioId)->select('id', 'procedimiento')->get();
        if($this->tiene_receta->count()){
            $this->recetaId = $this->tiene_receta[0]->id;
        }

        $info = DetReceta::join('recetas as r', 'r.id', 'det_recetas.receta_id')
            ->where('r.producto_id', $this->id_prod_receta)
            ->where('r.comercio_id', $this->comercioId)
            ->select('det_recetas.*', DB::RAW("'' as descripcion"), DB::RAW("'' as precio_costo"),
                DB::RAW("'' as importe"))->get();
        $this->total = 0;
        if($info->count()){
            foreach ($info as $i){
                $producto = Producto::find($i->producto_id);
                $i->descripcion = $producto->descripcion;
                $i->precio_costo = $producto->precio_costo;
                $i->importe = $i->cantidad * $producto->precio_costo;

                $this->total = $this->total + $i->importe;
            }
        }
        return view('livewire.recetas.component', ['info' => $info]);
    }
    public function doAction($action)
    {
        $this->action = $action;
        if($action == 1) $this->resetinput();
        else {
            $procedimiento = Receta::where('producto_id', $this->id_prod_receta)
                ->where('comercio_id', $this->comercioId)->select('procedimiento')->first();
            if($procedimiento) $this->procedimiento = $procedimiento->procedimiento;
        }
    }
    public function resetInput(){
        $this->cantidad            = null;
        $this->unidad              = 'Elegir';
        $this->producto            = 'Elegir';
        $this->selected_id         = null;
    }
    protected $listeners = [
        'calcular_precio_venta'     => 'calcular_precio_venta',
        'actualizarPreciosCargados' => 'actualizarPreciosCargados'
	];
	public function actualizarPreciosCargados()
	{
		//ACTUALIZO LOS IMPORTES QUE FIGUREN EN LOS DETALLE DE FACTURA ABIERTA O PENDIENTE
		//EN DONDE CONTENGAN AL PRODUCTO QUE ESTAMOS MODIFICANDO
		DB::begintransaction();
        try{
			$detalle = Factura::join('detfacturas as df', 'df.factura_id', 'facturas.id')
				->where('facturas.estado', 'abierta')
				->where('facturas.comercio_id', $this->comercioId)
				->where('df.producto_id', $this->id_prod_receta)
				->orWhere('facturas.estado', 'pendiente')
				->where('facturas.comercio_id', $this->comercioId)
				->where('df.producto_id', $this->id_prod_receta)
				->select('df.id', 'df.precio')->get();
			if($detalle->count()){
				foreach ($detalle as $i) {
					$grabar = Detfactura::find($i->id);
					if($i->mesa_id) $grabar->update(['precio' => $this->precio_venta_l1]);
					else $grabar->update(['precio' => $this->precio_venta_l2]);	
				}
			}
			session()->flash('msg-ok', 'Facturas actualizadas exitosamente!!!');
			DB::commit();
		}catch (\Exception $e){
			DB::rollback();
			session()->flash('msg-error', '¡¡¡ATENCIÓN!!! Las Facturas no fueron actualizadas...');
		}
		$this->resetInput();
		return;
	}
    public function edit($id)
    {
        $record = DetReceta::findOrFail($id);
        $this->selected_id = $id;
        $this->cantidad    = $record->cantidad;
        $this->unidad      = $record->unidad_de_medida;
        $this->producto    = $record->producto_id;
        //calculo el importe del item a editar de la receta para luego descontar del total
        $producto = Producto::find($this->producto);
        $precio_costo = $producto->precio_costo;
        $this->importe_anterior = $this->cantidad * $precio_costo;
    }
    public function calcular_precio_venta($data_cambios, $accion, $id, $comentario)
    {
        if($data_cambios){
            $this->preguntarPorPrecio = 'no';
            $this->cambiar_precios = $data_cambios;
        }
        if($accion == 'agregar'){    //si agrego un item
            $this->validate([
                'cantidad' => 'required|numeric|min:0|not_in:0',
                'unidad'   => 'not_in:Elegir|required',
                'producto' => 'not_in:Elegir|required'
            ]);
            //calculo variables para luego actualizar precio de costo y venta del 'id_prod_receta'
            $producto = Producto::find($this->producto);
            $cantidad = $this->cantidad;
            $precio_costo = $producto->precio_costo;
            $importe = $cantidad * $precio_costo;
            if($this->selected_id) $precio_costo = $this->total - $this->importe_anterior + $importe;
            else $precio_costo = $this->total + $importe;
        }else{     //si elimino un item
            $this->comentario = $comentario;
            //calculo variables para luego actualizar precios de costo y venta del producto
            $detReceta = DetReceta::find($id);
            $cantidad = $detReceta->cantidad;
            $producto = $detReceta->producto_id;

            $producto = Producto::find($producto);
            $precio_costo = $producto->precio_costo;
            $importe = $cantidad * $precio_costo;
            $precio_costo = $this->total - $importe;
        }

        if ($this->calcular_precio_de_venta == 0){
            //calcula el precio de venta sumando el margen de ganancia al costo del producto
            $this->precio_venta_sug_l1 = ($precio_costo * $this->porcentaje_margen_1) / 100 + $precio_costo;
            $this->precio_venta_sug_l2 = ($precio_costo * $this->porcentaje_margen_2) / 100 + $precio_costo;
            $this->precio_venta_l1 = ($precio_costo * $this->porcentaje_margen_1) / 100 + $precio_costo;
            $this->precio_venta_l2 = ($precio_costo * $this->porcentaje_margen_2) / 100 + $precio_costo;
        }else{
            //calcula el precio de venta obteniendo el margen de ganancia sobre el mismo
            $this->precio_venta_sug_l1 = $precio_costo * 100 / (100 - $this->porcentaje_margen_1);
            $this->precio_venta_sug_l2 = $precio_costo * 100 / (100 - $this->porcentaje_margen_2);
            $this->precio_venta_l1 = $precio_costo * 100 / (100 - $this->porcentaje_margen_1);
            $this->precio_venta_l2 = $precio_costo * 100 / (100 - $this->porcentaje_margen_2);
        }
        if ($this->redondear_precio_de_venta == 1){
            $this->precio_venta_sug_l1 = round($this->precio_venta_sug_l1, 0);
            $this->precio_venta_sug_l2 = round($this->precio_venta_sug_l2, 0);
            $this->precio_venta_l1 = round($this->precio_venta_l1, 0);
            $this->precio_venta_l2 = round($this->precio_venta_l2, 0);
        }
        $this->precioCosto = $precio_costo;
        if($accion == 'agregar') $this->StoreOrUpdate();    //si agrego un item
        else $this->destroy($id);                            //si elimino un item

    }
    public function StoreOrUpdate()
    {
        DB::begintransaction();
        try{
            if($this->selected_id){    //si modifico un item
                $receta = DetReceta::find($this->selected_id);
                $receta->update([
                    'cantidad'         => $this->cantidad,
                    'unidad_de_medida' => $this->unidad,
                    'producto_id'      => $this->producto
                ]);
            }else{       //si estoy agregando un item
                if(!$this->recetaId){
                    $receta = Receta::create([
                        'producto_id' => $this->id_prod_receta,
                        'comercio_id' => $this->comercioId
                    ]);
                    $receta =  DetReceta::create([
                        'receta_id'        => $receta->id,
                        'cantidad'         => $this->cantidad,
                        'unidad_de_medida' => $this->unidad,
                        'producto_id'      => $this->producto,
                        'comercio_id'      => $this->comercioId
                    ]);
                }else{
                    $receta =  DetReceta::create([
                        'receta_id'        => $this->recetaId,
                        'cantidad'         => $this->cantidad,
                        'unidad_de_medida' => $this->unidad,
                        'producto_id'      => $this->producto,
                        'comercio_id'      => $this->comercioId
                    ]);
                }
            }
            //en todos los casos modifico los precios del Producto Final según lo elegido
            $producto = Producto::find($this->id_prod_receta);
            if($this->cambiar_precios == 'cambiar_todo'){  //modifica precios sugeridos y de lista
                $producto->update([
                    'precio_costo'        => $this->precioCosto,
                    'precio_venta_sug_l1' => $this->precio_venta_sug_l1,
                    'precio_venta_sug_l2' => $this->precio_venta_sug_l2,
                    'precio_venta_l1'     => $this->precio_venta_l1,
                    'precio_venta_l2'     => $this->precio_venta_l2
                ]);
            }else{       				//solo modifica precios sugeridos
                $producto->update([
                    'precio_costo'        => $this->precioCosto,
                    'precio_venta_sug_l1' => $this->precio_venta_sug_l1,
                    'precio_venta_sug_l2' => $this->precio_venta_sug_l2
                ]);
            }
            //VERIFICO LOS DETALLES DE FACTURA ABIERTA O PENDIENTE QUE CONTENGAN AL PRODUCTO(materia prima)
            //QUE ESTAMOS CREANDO O MODIFICANDO PARA LUEGO PREGUNTAR SI SE QUIEREN MODIFICAR LAS MISMAS O NO
            $this->detalleProductoCargado = Factura::join('detfacturas as df', 'df.factura_id', 'facturas.id')
                ->where('facturas.estado', 'abierta')
                ->where('facturas.comercio_id', $this->comercioId)
                ->where('df.producto_id', $this->id_prod_receta)
                ->orWhere('facturas.estado', 'pendiente')
                ->where('facturas.comercio_id', $this->comercioId)
                ->where('df.producto_id', $this->id_prod_receta)
                ->select('df.id', 'df.precio')->get();

            if($this->selected_id) session()->flash('msg-ok', 'Item Actualizado');
            else session()->flash('msg-ok', 'Item Creado');
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        if($this->detalleProductoCargado && $this->detalleProductoCargado->count()){
			$this->emit('cambiarPrecioDetalle', $this->detalleProductoCargado->count());
		}else{
			$this->resetInput();
			return;
		}
    }
    public function GrabarProcedimiento()
    {
        $this->validate(['procedimiento' => 'required']);

        DB::begintransaction();
        try{
            if($this->recetaId) {
                $record = Receta::find($this->recetaId);
                $record->update(['procedimiento' => $this->procedimiento]);
                $this->action = 1;
            }else {
                $receta =  Receta::create([
                    'producto_id'   => $this->id_prod_receta,
                    'procedimiento' => $this->procedimiento,
                    'comercio_id'   => $this->comercioId
                ]);
            }
            if($this->selected_id) session()->flash('msg-ok', 'Procedimiento Actualizado');
            else session()->flash('msg-ok', 'Procedimiento Creado');

            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        $this->resetInput();
        return;
    }
    public function GrabarPrincipal($data, $id)
    {
        DB::begintransaction();
        try{
            $record = DetReceta::find($id);
            $record->update(['principal' => $data]);

            session()->flash('msg-ok', 'Registro Actualizado');
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
    }
    public function destroy($id)
    {
        if ($id) {
            DB::begintransaction();
            try{
                //en todos los casos modifico los precios del 'id_prod_receta' según lo elegido
                $producto = Producto::find($this->id_prod_receta);
                if($this->cambiar_precios == 'cambiar_todo'){  //modifica precios sugeridos y de lista
                    $producto->update([
                        'precio_costo'        => $this->precioCosto,
                        'precio_venta_sug_l1' => $this->precio_venta_sug_l1,
                        'precio_venta_sug_l2' => $this->precio_venta_sug_l2,
                        'precio_venta_l1'     => $this->precio_venta_l1,
                        'precio_venta_l2'     => $this->precio_venta_l2
                    ]);
                }else{       				//solo modifica precios sugeridos
                    $producto->update([
                        'precio_costo'        => $this->precioCosto,
                        'precio_venta_sug_l1' => $this->precio_venta_sug_l1,
                        'precio_venta_sug_l2' => $this->precio_venta_sug_l2
                    ]);
                }

                //elimino item de la receta
                $receta = DetReceta::find($id)->delete();
                $audit = Auditoria::create([
                    'item_deleted_id' => $id,
                    'tabla'           => 'Detalle/Recetas',
                    'estado'          => '0',
                    'user_delete_id'  => auth()->user()->id,
                    'comentario'      => $this->comentario,
                    'comercio_id'     => $this->comercioId
                ]);
                session()->flash('msg-ok', 'Registro eliminado con éxito!!');
                DB::commit();
            }catch (\Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se eliminó...');
            }
            return;
        }
    }
}
