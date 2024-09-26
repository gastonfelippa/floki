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
use App\Models\Salsa;
use DB;


class RecetaController extends Component
{
    public $comercioId, $id_prod_receta, $id_salsa_receta, $info;
    public $cantidad, $total, $total_porcion, $comentario, $action = 1, $selected_id;
    public $producto, $productos, $prod_receta, $prod, $salsa_receta;
    public $presentacion, $unidad_medida_presentacion, $porciones, $porcion, $habilitar_porciones;
    public $porcentaje, $calcular_precio_de_venta, $redondear_precio_de_venta, $categoria;
    public $precioCosto, $precio_venta_sug_l1, $precio_venta_sug_l2, $precio_venta_l1, $precio_venta_l2;
    public $porcentaje_margen_1, $porcentaje_margen_2, $importe_anterior, $preguntarPorPrecio;
    public $recetaId, $tiene_receta, $procedimiento, $cambiar_precios, $detalleProductoCargado;
    public $precio_venta_sub_receta_l1, $precio_venta_sub_receta_l2, $precio_venta_sub_receta_sug_l1, $precio_venta_sub_receta_sug_l2;
    public $nueva_porcion;

    public function mount()
    {
        //$this->unidad = 'Elegir';
        $this->info = [];
        $this->producto = 'Elegir';
        $this->prod = 'Helado 1 Bocha';
        $this->habilitar_porciones = false;        
        $this->precio_venta_sug_l1 = 0; 
        $this->precio_venta_sug_l2 = 0; 
        $this->precio_venta_l1 = 0; 
        $this->precio_venta_l2 = 0;

        $this->comercioId = session('idComercio');
        $this->id_prod_receta = session('producto_receta_id');
        $this->id_salsa_receta = session('salsa_receta_id');

        $comercio = Comercio::find($this->comercioId);
        $this->calcular_precio_de_venta = $comercio->calcular_precio_de_venta;
        $this->redondear_precio_de_venta = $comercio->redondear_precio_de_venta;         
        
    }

    public function render()
    {
        if(!$this->cambiar_precios) $this->cambiar_precios = 'solo_costos';
        if(!$this->preguntarPorPrecio) $this->preguntarPorPrecio = 'si';

        $this->buscarDetalleReceta()
;
        return view('livewire.recetas.component');
        // return view('livewire.recetas.component', ['info' => $info]);
    }
    public function buscarDetalleReceta()
    {
        if ($this->id_prod_receta){
            $producto = Producto::find($this->id_prod_receta);
            $this->prod_receta = $producto->descripcion;
            $this->categoria = $producto->categoria_id;        

            $porcentaje = Categoria::where('id', $this->categoria)->select('margen_1', 'margen_2')->get();
            $this->porcentaje_margen_1 = $porcentaje[0]->margen_1;
            $this->porcentaje_margen_2 = $porcentaje[0]->margen_2;

            $this->productos = Producto::where('comercio_id', $this->comercioId)
                                ->where('estado', 'Disponible')
                                ->where('id', 'not like', $this->id_prod_receta)->orderBy('descripcion')->get();

            $this->tiene_receta = Receta::where('producto_id', $this->id_prod_receta)
                ->where('comercio_id', $this->comercioId)->select('id', 'porciones', 'procedimiento')->get();
            if($this->tiene_receta->count()){
                $this->recetaId = $this->tiene_receta[0]->id;
                $this->porciones= $this->tiene_receta[0]->porciones;
            }

            
            $this->info = DetReceta::join('recetas as r', 'r.id', 'det_recetas.receta_id')
                ->where('r.producto_id', $this->id_prod_receta)
                ->where('r.comercio_id', $this->comercioId)
                ->select('det_recetas.*', DB::RAW("'' as descripcion"), DB::RAW("'' as precio_costo"),
                    DB::RAW("0 as merma"),DB::RAW("0 as cantidad_real"),DB::RAW("'' as importe"))->get();              
            $this->total = 0;
            if($this->info->count() > 0){
                foreach ($this->info as $i){
                    $producto = Producto::find($i->producto_id);
                    $i->descripcion = $producto->descripcion;
                    $i->precio_costo = $producto->precio_costo;

                    if($producto->merma > 0) $i->merma = $producto->merma;
                    else $i->merma = 0;
                
                    if ($producto->merma > 0) {
                        $i->cantidad_real = $i->cantidad + (($i->cantidad * $producto->merma)/100);
                    }else $i->cantidad_real = $i->cantidad;

                    $i->importe = ($i->cantidad_real * $producto->precio_costo)/$producto->presentacion;
                    $this->total = $this->total + $i->importe;               
                }
                if($this->total > 0) $this->total_porcion = $this->total / $this->porciones;
            }
        } else {
            $this->datos_receta_salsa();            
        }
    }
    public function datos_receta_salsa()
    {
        $salsa = Salsa::find($this->id_salsa_receta);
        $this->prod_receta = $salsa->descripcion;

        // $producto = Producto::find($this->id_prod_receta);
        // $this->prod_receta = $producto->descripcion;
        // $this->categoria = $producto->categoria_id;        

        // $porcentaje = Categoria::where('id', $this->categoria)->select('margen_1', 'margen_2')->get();
        // $this->porcentaje_margen_1 = $porcentaje[0]->margen_1;
        // $this->porcentaje_margen_2 = $porcentaje[0]->margen_2;

        $this->productos = Producto::where('comercio_id', $this->comercioId)
                            ->where('estado', 'Disponible')
                            ->orderBy('descripcion')->get();

        $this->tiene_receta = Receta::where('salsa_id', $this->id_salsa_receta)
            ->where('comercio_id', $this->comercioId)->select('id', 'porciones', 'procedimiento')->get();
        if($this->tiene_receta->count()){
            $this->recetaId = $this->tiene_receta[0]->id;
            $this->porciones= $this->tiene_receta[0]->porciones;
        }

        $this->info = DetReceta::join('recetas as r', 'r.id', 'det_recetas.receta_id')
            ->where('r.salsa_id', $this->id_salsa_receta)
            ->where('r.comercio_id', $this->comercioId)
            ->select('det_recetas.*', DB::RAW("'' as descripcion"), DB::RAW("'' as precio_costo"),
                DB::RAW("0 as merma"),DB::RAW("0 as cantidad_real"),DB::RAW("'' as importe"))->get();              
        $this->total = 0;
        if($this->info->count() > 0){
            foreach ($this->info as $i){
                $producto = Producto::find($i->producto_id);
                $i->descripcion = $producto->descripcion;
                $i->precio_costo = $producto->precio_costo;

                if($producto->merma > 0) $i->merma = $producto->merma;
                else $i->merma = 0;
            
                if ($producto->merma > 0) {
                    $i->cantidad_real = $i->cantidad + (($i->cantidad * $producto->merma)/100);
                }else $i->cantidad_real = $i->cantidad;

                $i->importe = ($i->cantidad_real * $producto->precio_costo)/$producto->presentacion;
                $this->total = $this->total + $i->importe;               
            }
            if($this->total > 0) $this->total_porcion = $this->total / $this->porciones;
        }
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
    public function resetInput()
    {
        $this->cantidad                   = null;
        //$this->unidad                     = 'Elegir';
        $this->producto                   = 'Elegir';
        $this->presentacion               = '';
        $this->unidad_medida_presentacion = null;
        $this->producto                   = 'Elegir';
        $this->selected_id                = null;
        $this->habilitar_porciones        = false;
        $this->emit('focus');
    }
    protected $listeners = [
        'calcular_precio_venta',
        'actualizarPreciosCargados',
        'buscar_producto',
        'habilitar_porciones',
        'comparar_porciones',
        'resetPorciones',
        'actualizarPorciones'
	];
    public function resetPorciones()
    {
        $this->habilitar_porciones = false;
    }
    public function habilitar_porciones()
    {
        $this->nueva_porcion = $this->porciones;
        $this->habilitar_porciones = true;
    }
    public function comparar_porciones()
    {
        if($this->total > 0) if($this->nueva_porcion != $this->porciones) $this->emit('actualizar_porciones');
    }
    public function buscar_producto()
    {
        $producto = Producto::where('id', $this->producto)->select('presentacion', 'unidad_de_medida')->first();
        if ($producto){
            $this->presentacion = $producto->presentacion;
            $this->unidad_medida_presentacion = $producto->unidad_de_medida;
        }else $this->resetInput();  
    }
    // public function verificar_unidades()
    // {
    //     if($this->unidad != 'Elegir' && $this->unidad_medida_presentacion){
    //         if($this->unidad != $this->unidad_medida_presentacion){
    //             $this->unidad = 'Elegir';
    //             $this->emit('unidadesDeMedidaDiferentes');
    //         } 
    //     }
    // }
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
				->select('df.id', 'facturas.mesa_id')->get();
			if($detalle->count()){
				foreach ($detalle as $i) {
					$grabar = Detfactura::find($i->id);
					if($i->mesa_id) $grabar->update(['precio' => $this->precio_venta_l1]);
					else $grabar->update(['precio' => $this->precio_venta_l2]);	
				}
			}
            $this->emit('facturasActualizadas');
			DB::commit();
		}catch (\Exception $e){
			DB::rollback();
			session()->flash('msg-error', '¡¡¡ATENCIÓN!!! Las Facturas no fueron actualizadas...');
		}
		$this->resetInput();
		return;
	}
    public function actualizarPorciones()
    {
        DB::begintransaction();
        try{
            $receta = Receta::find($this->recetaId);
            $receta->update(['porciones' => $this->nueva_porcion]);
            $this->total_porcion = $this->total / $this->nueva_porcion;
            
            //calculo los precios a modificar
            $this->calcularPrecios($this->total_porcion);

            if ($this->id_prod_receta) {
                 //actualizo los precios del producto de la receta
                $this->actualizarPreciosProductoReceta();

                //actualizo precios de recetas en donde este producto aparezca como ingrediente
               // $this->actualizarRecetasRelacionadas();
            }
                    

            session()->flash('msg-ok', 'Porción Actualizada');
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        $this->resetInput();
    }
    public function edit($id)
    {
        $record = DetReceta::findOrFail($id);
        $this->selected_id = $id;
        $this->cantidad    = $record->cantidad;
        //$this->unidad      = $record->unidad_de_medida;
        $this->producto    = $record->producto_id;

        //calculo el importe del item a editar de la receta para luego descontar del total
        $producto = Producto::find($this->producto);
        $cantidad = $this->cantidad;
        $precio_costo = $producto->precio_costo;
        $this->presentacion = $producto->presentacion;
        $this->unidad_medida_presentacion = $producto->unidad_de_medida;
      
        if ($producto->merma > 0) {
            $cantidad_real = $cantidad + (($cantidad * $producto->merma)/100);
        }else $cantidad_real = $cantidad;
        $this->importe_anterior = ($cantidad_real * $producto->precio_costo)/$producto->presentacion;
    }
    public function calcular_precio_venta($data_cambios, $accion, $id, $comentario)
    {
        if($data_cambios){
            $this->preguntarPorPrecio = 'no';
            $this->cambiar_precios = $data_cambios;
        }
        if($accion == 'agregar'){    //si agrego o modifico un item
            $this->validate([
                'cantidad' => 'required|numeric|min:0|not_in:0',
                'producto' => 'not_in:Elegir|required'
            ]);
            //calculo variables para luego actualizar precio de costo y venta del 'id_prod_receta'
            $producto = Producto::find($this->producto);
            $cantidad = $this->cantidad;
            $precio_costo = $producto->precio_costo;
          
            if ($producto->merma > 0) {
                $cantidad_real = $cantidad + (($cantidad * $producto->merma)/100);
            }else $cantidad_real = $cantidad;
            $importe = ($cantidad_real * $producto->precio_costo)/$producto->presentacion;

            if($this->selected_id) $precio_costo = $this->total - $this->importe_anterior + $importe;
            else $precio_costo = $this->total + $importe;  //costo de esta receta
        }else{     //si elimino un item
            $this->comentario = $comentario;
            //calculo variables para luego actualizar precios de costo y venta del producto
            $detReceta = DetReceta::find($id);
            $cantidad = $detReceta->cantidad;
            $producto = $detReceta->producto_id;

            $producto = Producto::find($producto);
            $precio_costo = $producto->precio_costo;

            if ($producto->merma > 0) {
                $cantidad_real = $cantidad + (($cantidad * $producto->merma)/100);
            }else $cantidad_real = $cantidad;
            $importe = ($cantidad_real * $producto->precio_costo)/$producto->presentacion; 
            $precio_costo = $this->total - $importe;
        }
        
        if($this->total > 0) $this->total_porcion = $precio_costo / $this->porciones;
        else $this->total_porcion = $precio_costo / $this->nueva_porcion;

        if($accion == 'agregar') $this->StoreOrUpdate();    //si agrego un item
        else $this->destroy($id);                            //si elimino un item

    }
    public function calcularPrecios($precioDeCosto)
    {
        if($this->porcentaje_margen_1 > 0) {
            //LA VARIABLE '$precio_costo' HACE REFERENCIA AL PRECIO DE COSTO DEL PRODUCTO CABECERA DE LA RECETA
            if ($this->calcular_precio_de_venta == 0){
                //calcula el precio de venta sumando el margen de ganancia al costo del producto
                $this->precio_venta_sug_l1 = ($precioDeCosto * $this->porcentaje_margen_1) / 100 + $precioDeCosto;
                $this->precio_venta_sug_l2 = ($precioDeCosto * $this->porcentaje_margen_2) / 100 + $precioDeCosto;
                $this->precio_venta_l1 = ($precioDeCosto * $this->porcentaje_margen_1) / 100 + $precioDeCosto;
                $this->precio_venta_l2 = ($precioDeCosto * $this->porcentaje_margen_2) / 100 + $precioDeCosto;
            }else{
                //calcula el precio de venta obteniendo el margen de ganancia sobre el mismo
                $this->precio_venta_sug_l1 = $precioDeCosto * 100 / (100 - $this->porcentaje_margen_1);
                $this->precio_venta_sug_l2 = $precioDeCosto * 100 / (100 - $this->porcentaje_margen_2);
                $this->precio_venta_l1 = $precioDeCosto * 100 / (100 - $this->porcentaje_margen_1);
                $this->precio_venta_l2 = $precioDeCosto * 100 / (100 - $this->porcentaje_margen_2);
            }
            if ($this->redondear_precio_de_venta == 1){
                $this->precio_venta_sug_l1 = round($this->precio_venta_sug_l1, 0);
                $this->precio_venta_sug_l2 = round($this->precio_venta_sug_l2, 0);
                $this->precio_venta_l1 = round($this->precio_venta_l1, 0);
                $this->precio_venta_l2 = round($this->precio_venta_l2, 0);
            }
        }
        $this->precioCosto = $precioDeCosto;        
    }
    public function calcularPreciosElaborados($precioDeCosto)
    {
        // //LA VARIABLE '$precio_costo' HACE REFERENCIA AL PRECIO DE COSTO DEL PRODUCTO CABECERA DE LA RECETA
        // if ($this->calcular_precio_de_venta == 0){
        //     //calcula el precio de venta sumando el margen de ganancia al costo del producto
        //     $this->precio_venta_sug_l1 = ($precioDeCosto * $this->porcentaje_margen_1) / 100 + $precioDeCosto;
        //     $this->precio_venta_sug_l2 = ($precioDeCosto * $this->porcentaje_margen_2) / 100 + $precioDeCosto;
        //     $this->precio_venta_l1 = ($precioDeCosto * $this->porcentaje_margen_1) / 100 + $precioDeCosto;
        //     $this->precio_venta_l2 = ($precioDeCosto * $this->porcentaje_margen_2) / 100 + $precioDeCosto;
        // }else{
        //     //calcula el precio de venta obteniendo el margen de ganancia sobre el mismo
        //     $this->precio_venta_sug_l1 = $precioDeCosto * 100 / (100 - $this->porcentaje_margen_1);
        //     $this->precio_venta_sug_l2 = $precioDeCosto * 100 / (100 - $this->porcentaje_margen_2);
        //     $this->precio_venta_l1 = $precioDeCosto * 100 / (100 - $this->porcentaje_margen_1);
        //     $this->precio_venta_l2 = $precioDeCosto * 100 / (100 - $this->porcentaje_margen_2);
        // }
        // if ($this->redondear_precio_de_venta == 1){
        //     $this->precio_venta_sug_l1 = round($this->precio_venta_sug_l1, 0);
        //     $this->precio_venta_sug_l2 = round($this->precio_venta_sug_l2, 0);
        //     $this->precio_venta_l1 = round($this->precio_venta_l1, 0);
        //     $this->precio_venta_l2 = round($this->precio_venta_l2, 0);
        // }
        $this->precioCosto = $precioDeCosto;        
    }
    public function StoreOrUpdate()
    {
        if ($this->id_prod_receta) $this->grabar_receta_producto();
        else $this->grabar_receta_salsa();       
    }
    public function grabar_receta_producto()
    {
        //$this->verificar_unidades();
        DB::begintransaction();
        try{
            if($this->selected_id){    //si modifico un item
                $receta = DetReceta::find($this->selected_id);
                $receta->update([
                    'cantidad'         => $this->cantidad,
                    'unidad_de_medida' => $this->unidad_medida_presentacion,
                    'producto_id'      => $this->producto
                ]);
            }else{       //si estoy agregando un item
                if(!$this->recetaId){
                    $receta = Receta::create([
                        'producto_id' => $this->id_prod_receta,
                        'porciones'   => $this->nueva_porcion,
                        'comercio_id' => $this->comercioId
                    ]);
                    $receta =  DetReceta::create([
                        'receta_id'        => $receta->id,
                        'cantidad'         => $this->cantidad,
                        'unidad_de_medida' => $this->unidad_medida_presentacion,
                        'producto_id'      => $this->producto,
                        'comercio_id'      => $this->comercioId
                    ]);
                }else{
                    $receta =  DetReceta::create([
                        'receta_id'        => $this->recetaId,
                        'cantidad'         => $this->cantidad,
                        'unidad_de_medida' => $this->unidad_medida_presentacion,
                        'producto_id'      => $this->producto,
                        'comercio_id'      => $this->comercioId
                    ]);
                }
            }
    
            //calculo los precios a modificar
            $this->calcularPrecios($this->total_porcion);

            if ($this->id_prod_receta) {
                //actualizo los precios del producto de la receta
                $this->actualizarPreciosProductoReceta();
            
                //actualizo precios de recetas en donde este producto aparezca como ingrediente 
                $this->actualizarRecetasRelacionadas();
            }

            //Si modifico los Precios de Lista, verifico los Detalle de Factura Abierta o Pendiente
            //que contengan este Producto cargado. Luego pregunto si se quieren modificar las mismas o no.
            if($this->cambiar_precios == 'cambiar_todo'){
                $this->detalleProductoCargado = Factura::join('detfacturas as df', 'df.factura_id', 'facturas.id')
                    ->where('facturas.estado', 'abierta')
                    ->where('facturas.comercio_id', $this->comercioId)
                    ->where('df.producto_id', $this->id_prod_receta)
                    ->orWhere('facturas.estado', 'pendiente')
                    ->where('facturas.comercio_id', $this->comercioId)
                    ->where('df.producto_id', $this->id_prod_receta)
                    ->select('df.id')->get();
            }

            if($this->selected_id) session()->flash('msg-ok', 'Item Actualizado');
            else session()->flash('msg-ok', 'Item Creado');
            DB::commit();
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        if($this->detalleProductoCargado && $this->detalleProductoCargado->count()){
			//$this->emit('cambiarPrecioDetalle', $this->detalleProductoCargado->count());
            $this->resetInput();
			return;
		}else{
			$this->resetInput();
			return;
		}
    }
    public function grabar_receta_salsa()
    {
        //$this->verificar_unidades();
        DB::begintransaction();
        try{
            if($this->selected_id){    //si modifico un item
                $receta = DetReceta::find($this->selected_id);
                $receta->update([
                    'cantidad'         => $this->cantidad,
                    'unidad_de_medida' => $this->unidad_medida_presentacion,
                    'producto_id'      => $this->producto
                ]);
            }else{       //si estoy agregando un item
                if(!$this->recetaId){
                    $receta = Receta::create([
                        'salsa_id'    => $this->id_salsa_receta,
                        'porciones'   => $this->nueva_porcion,
                        'comercio_id' => $this->comercioId
                    ]);
                    $receta =  DetReceta::create([
                        'receta_id'        => $receta->id,
                        'cantidad'         => $this->cantidad,
                        'unidad_de_medida' => $this->unidad_medida_presentacion,
                        'producto_id'      => $this->producto,
                        'comercio_id'      => $this->comercioId
                    ]);
                }else{
                    $receta =  DetReceta::create([
                        'receta_id'        => $this->recetaId,
                        'cantidad'         => $this->cantidad,
                        'unidad_de_medida' => $this->unidad_medida_presentacion,
                        'producto_id'      => $this->producto,
                        'comercio_id'      => $this->comercioId
                    ]);
                }
            }
    
            //calculo los precios a modificar
            $this->calcularPrecios($this->total_porcion);

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
                // //modifico los precios del 'id_prod_receta' según lo elegido
                // $producto = Producto::find($this->id_prod_receta);
                // if($this->cambiar_precios == 'cambiar_todo'){  //modifica precios sugeridos y de lista
                //     $producto->update([
                //         'precio_costo'        => $this->precioCosto,
                //         'precio_venta_sug_l1' => $this->precio_venta_sug_l1,
                //         'precio_venta_sug_l2' => $this->precio_venta_sug_l2,
                //         'precio_venta_l1'     => $this->precio_venta_l1,
                //         'precio_venta_l2'     => $this->precio_venta_l2
                //     ]);
                // }else{       				//solo modifica precios sugeridos
                //     $producto->update([
                //         'precio_costo'        => $this->precioCosto,
                //         'precio_venta_sug_l1' => $this->precio_venta_sug_l1,
                //         'precio_venta_sug_l2' => $this->precio_venta_sug_l2
                //     ]);
                // }
                //calculo los precios a modificar
                $this->calcularPrecios($this->total_porcion);

                //elimino item de la receta
                $receta = DetReceta::find($id)->delete();

                //actualizo los precios del producto de la receta
                $this->actualizarPreciosProductoReceta();

                $this->actualizarRecetasRelacionadas(); 

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
    public function actualizarPreciosProductoReceta()
    {
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
        };
    }
    public function actualizarRecetasRelacionadas()
    { 
        $sub_receta = DetReceta::join('recetas as r', 'r.id', 'det_recetas.receta_id')
            ->where('det_recetas.producto_id', $this->id_prod_receta)
            ->where('det_recetas.comercio_id', $this->comercioId)
            ->select('det_recetas.receta_id', 'r.producto_id')->get();
        if($sub_receta->count()){              
            foreach ($sub_receta as $i){   
                $sub_receta = DetReceta::where('receta_id', $i->receta_id)
                    ->where('comercio_id', $this->comercioId)
                    ->select('cantidad', 'unidad_de_medida', 'producto_id')->get();
                $precio_costo_sub_receta = 0;
                $this->precio_venta_sub_receta_sug_l1 = null;
                $this->precio_venta_sub_receta_sug_l2 = null;
                $this->precio_venta_sub_receta_l1 = null;
                $this->precio_venta_sub_receta_l2 = null;
                foreach ($sub_receta as $j) {
                    $producto = Producto::where('id', $j->producto_id)   
                        ->select('descripcion','precio_costo', 'merma', 'presentacion')->first();
                    if ($producto->merma > 0) {
                        $cantidad_real = $j->cantidad + (($j->cantidad * $producto->merma)/100);
                    }else $cantidad_real = $j->cantidad;
                    $importe_item_receta = ($cantidad_real * $producto->precio_costo)/$producto->presentacion;
                    $precio_costo_sub_receta += $importe_item_receta;
                }  
                        
                //LA VARIABLE '$precio_costo_sub_receta' HACE REFERENCIA AL PRECIO DE COSTO DEL PRODUCTO 
                //QUE CONTIENE AL PRODUCTO CABECERA COMO INGREDIENTE DE SU PROPIA RECETA
                if ($this->calcular_precio_de_venta == 0){
                    //calcula el precio de venta sumando el margen de ganancia al costo del producto
                    $this->precio_venta_sub_receta_sug_l1 = ($precio_costo_sub_receta * $this->porcentaje_margen_1) / 100 + $precio_costo_sub_receta;
                    $this->precio_venta_sub_receta_sug_l2 = ($precio_costo_sub_receta * $this->porcentaje_margen_2) / 100 + $precio_costo_sub_receta;
                    $this->precio_venta_sub_receta_l1 = ($precio_costo_sub_receta * $this->porcentaje_margen_1) / 100 + $precio_costo_sub_receta;
                    $this->precio_venta_sub_receta_l2 = ($precio_costo_sub_receta * $this->porcentaje_margen_2) / 100 + $precio_costo_sub_receta;
                }else{
                    //calcula el precio de venta obteniendo el margen de ganancia sobre el mismo
                    $this->precio_venta_sub_receta_sug_l1 = $precio_costo_sub_receta * 100 / (100 - $this->porcentaje_margen_1);
                    $this->precio_venta_sub_receta_sug_l2 = $precio_costo_sub_receta * 100 / (100 - $this->porcentaje_margen_2);
                    $this->precio_venta_sub_receta_l1 = $precio_costo_sub_receta * 100 / (100 - $this->porcentaje_margen_1);
                    $this->precio_venta_sub_receta_l2 = $precio_costo_sub_receta * 100 / (100 - $this->porcentaje_margen_2);
                }
                if ($this->redondear_precio_de_venta == 1){
                    $this->precio_venta_sub_receta_sug_l1 = round($this->precio_venta_sub_receta_sug_l1, 0);
                    $this->precio_venta_sub_receta_sug_l2 = round($this->precio_venta_sub_receta_sug_l2, 0);
                    $this->precio_venta_sub_receta_l1 = round($this->precio_venta_sub_receta_l1, 0);
                    $this->precio_venta_sub_receta_l2 = round($this->precio_venta_sub_receta_l2, 0);
                }
                $producto_sub_receta = Producto::find($i->producto_id);
                if($this->cambiar_precios == 'cambiar_todo'){ 
                    $producto_sub_receta->update([
                        'precio_costo'        => $precio_costo_sub_receta,
                        'precio_venta_sug_l1' => $this->precio_venta_sub_receta_sug_l1,
                        'precio_venta_sug_l2' => $this->precio_venta_sub_receta_sug_l2,
                        'precio_venta_l1'     => $this->precio_venta_sub_receta_l1,
                        'precio_venta_l2'     => $this->precio_venta_sub_receta_l2
                    ]);
                }else{       			
                    $producto_sub_receta->update([
                        'precio_costo'        => $precio_costo_sub_receta,
                        'precio_venta_sug_l1' => $this->precio_venta_sub_receta_sug_l1,
                        'precio_venta_sug_l2' => $this->precio_venta_sub_receta_sug_l2,
                    ]);
                }
            }
        } 
    }
}
