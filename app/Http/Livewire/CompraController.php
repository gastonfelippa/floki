<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Traits\GenericTrait;
use App\Models\Auditoria;
use App\Models\Categoria;
use App\Models\Comercio;
use App\Models\Compra;
use App\Models\Detcompra;
use App\Models\Detfactura;
use App\Models\DetReceta;
use App\Models\Factura;
use App\Models\Historico;
use App\Models\Peps;
use App\Models\Producto;
use App\Models\ProductoProveedor;
use App\Models\Proveedor;
use App\Models\Receta;
use App\Models\Stock;
use Carbon\Carbon;
use DB;

class CompraController extends Component
{
    use GenericTrait;

    public $cantidad = 1, $precio, $estado='abierta';
    public $nombreProveedor, $proveedor="Elegir", $producto="Elegir", $barcode;
    public $proveedores, $productos;
    public $selected_id = null, $search, $numFactura, $action = 1, $mostrar_datos;
    public $compras, $total, $importe, $totalAgrabar;  
    public $inicio_factura = true, $habilitar_botones =null,$modificar, $codigo;
    public $comercioId, $comercioTipo, $factura_id;
    public $numeroFactura, $letra, $sucursal, $numFact, $fecha;
    public $f_de_pago = null, $nro_comp_pago = null, $comentarioPago = '', $mercadopago = null;
    public $mostrar_sp = 0, $tiene_sp, $es_producto = 1, $cambiar_precios;
    public $precio_venta_l1, $precio_venta_l2, $precio_venta_sug_l1, $precio_venta_sug_l2;
	public $calcular_precio_de_venta, $redondear_precio_de_venta, $costo_actual;
    public $costo_historico, $venta_sug_l1_historico, $venta_sug_l2_historico, $venta_l1_historico, $venta_l2_historico;
    public $detalleProductoCargado, $productoFinalId;
    public $dirProveedor, $nomCli;

	public function render()
	{       
        $this->comercioId = session('idComercio');
        $this->comercioTipo = session('tipoComercio');

        if($this->producto <> 'Elegir'){
            $producto = Producto::find($this->producto);
            $this->costo_actual = $producto->precio_costo;
        }

        $record = Comercio::find($this->comercioId);
		$this->calcular_precio_de_venta = $record->calcular_precio_de_venta;
		$this->redondear_precio_de_venta = $record->redondear_precio_de_venta;
		$this->redondear_precio_de_venta = $record->redondear_precio_de_venta;
        if(!$this->cambiar_precios){
            if($record->opcion_de_guardado_compra == '0') $this->cambiar_precios = 'no';
            elseif($record->opcion_de_guardado_compra == '1') $this->cambiar_precios = 'solo_costos';
            else $this->cambiar_precios = 'cambiar_todo';
        }
                
        $this->productos = Producto::where('comercio_id', $this->comercioId)
            ->where('tipo', '<>', 'Art. Venta c/receta')
            ->where('tipo', '<>', 'Art. Elaborado')
            ->where('estado', 'Disponible')
            ->orderBy('descripcion')->get();
        
        $this->proveedores = Proveedor::select()->where('comercio_id', $this->comercioId)->orderBy('nombre_empresa', 'asc')->get();
      
        $dProveedor = Proveedor::find($this->proveedor);
        if($dProveedor != null) {
            $this->nombreProveedor = $dProveedor->nombre_empresa;
            $this->dirProveedor = $dProveedor->calle . ' ' . $dProveedor->numero;
        }
        $encabezado = Compra::join('proveedores as prov','prov.id','compras.proveedor_id')
            ->join('localidades as loc','loc.id','prov.localidad_id')
            ->where('compras.estado','like','abierta')
            ->where('compras.comercio_id', $this->comercioId)
            ->select('compras.*','prov.nombre_empresa as nombre_empresa',
                    'prov.calle', 'prov.numero', 'loc.descripcion')->get();
                    
        if($encabezado->count()){
            //$this->action = 1;
            $this->inicio_factura = false;
            $this->factura_id = $encabezado[0]->id;                             
            $this->letra = $encabezado[0]->letra;
            $this->sucursal = $encabezado[0]->sucursal;
            $this->numFact = $encabezado[0]->num_fact;
            $this->fecha = Carbon::parse($encabezado[0]->fecha_fact)->format('d-m-Y');
            $this->proveedor = $encabezado[0]->proveedor_id;
            $this->nombreProveedor = $encabezado[0]->nombre_empresa;
        }

        $info = Detcompra::select('*')->where('comercio_id', $this->comercioId)->get();
        if($info->count()){ 
            $info = Detcompra::join('compras as r','r.id','det_compras.factura_id')
                ->select('det_compras.*', DB::RAW("'' as p_id"), 
                    DB::RAW("'' as codigo"), DB::RAW("'' as producto"), DB::RAW("'' as es_producto"))
                ->where('det_compras.factura_id', $this->factura_id)
                ->where('det_compras.comercio_id', $this->comercioId)
                ->orderBy('det_compras.id')->get();  
            
            $this->total = 0;
            $contador_filas = 0;
            foreach ($info as $i){
                $contador_filas ++;
                $i->importe=$i->cantidad * $i->precio;
                $this->total += $i->importe;

                $producto = Producto::find($i->producto_id);
                $i->p_id        = $producto->id;
                $i->codigo      = $producto->codigo;
                $i->producto    = $producto->descripcion;
                $i->es_producto = 1;
            }
        } 

        return view('livewire.compras.component', [
            'info' => $info,
            'encabezado' => $encabezado
        ]);
    }    
    protected $listeners = [
        'buscarProducto',
        'buscarPorCodigo',
        'buscarDomicilio',
        'CrearModificarEncabezado',
        'opcionCambiarPrecios',
        'actualizarPreciosCargados',
        'elegirFormaDePago',
        'factura_contado',
        'anularFactura',
        'deleteRow'                 => 'destroy'         
    ];
    public function buscarPorCodigo() 
    {
        if($this->barcode != null){
            $this->mostrar_sp = 0;
            $articulos = Producto::where('codigo', $this->barcode)
                            ->where('estado', 'Disponible')
                            ->where('comercio_id', $this->comercioId)->get();
            if ($articulos->count()) $this->producto = $articulos[0]->id;
            else session()->flash('msg-error', 'El Código no existe...');
        }
    }
    public function doAction($action)
    {
        $this->action = $action;
        if($this->action == 1) $this->resetInput();
    }
    public function resetInput()
    {
        $this->action      = 1;
        $this->cantidad    = 1;
        $this->barcode     = '';
        $this->precio      = '';
        $this->producto    = 'Elegir';
        $this->selected_id = null;
        $this->search      = '';
        $this->letra       = null;
        $this->sucursal    = null;
        $this->numFact     = null;
        $this->fecha       = '';
        $this->mostrar_sp  = 0;
        $this->es_producto = 1;
        $this->costo_actual = null;
    }
    public function resetInputTodos()
    {
        $this->action            = 3;
        $this->cantidad          = 1;
        $this->barcode           ='';
        $this->precio            = '';        
        $this->proveedor         = 'Elegir';
        $this->dirProveedor      = null;
        $this->producto          = 'Elegir';
        $this->selected_id       = null;
        $this->search            = '';
        $this->habilitar_botones = false;
        $this->letra             = null;
        $this->sucursal          = null;
        $this->numFact           = null;
        $this->fecha             = '';
        $this->mostrar_sp        = 0;
        $this->es_producto       = 1;
    }
    public function CrearModificarEncabezado($data)
    {       
        $info = json_decode($data);
        $this->letra = $info->letra;
        if($info->sucursal == '') $this->sucursal = null;
        else $this->sucursal = $info->sucursal;
        if($info->numero == '') $this->numFact = null;
        else $this->numFact = $info->numero;

        $this->numeroFactura = $this->sucursal . '-' . $this->numFact;
        $dataPro = Proveedor::find($info->proveedor_id);
        $this->nombreProveedor = $dataPro->nombre_empresa;           
        $this->fecha     = Carbon::now();
        if($info->fecha != '') $this->fecha = $info->fecha;
        
        $this->proveedor = $info->proveedor_id;
        
        if(!$this->inicio_factura){
            $record = Compra::find($this->factura_id);
            $record->update([
                'letra'        => $this->letra,
                'sucursal'     => $this->sucursal,
                'num_fact'     => $this->numFact,
                'fecha_fact'   => Carbon::parse($this->fecha)->format('Y,m,d H:i:s'),
                'proveedor_id' => $this->proveedor
            ]);
            session()->flash('message', 'Encabezado Modificado...');
        }
    }
    public function edit($id, $es_producto)
    {
        $this->selected_id = $id;
        $this->es_producto = $es_producto;
        $record = Detcompra::find($id);
        $this->producto = $record->producto_id;
        $this->precio   = $record->precio;
        $this->cantidad = $record->cantidad;
    }
    public function StoreOrUpdateButton($articuloId)
    {
        if($articuloId == 0 && $this->es_producto == 1){
            $this->validate([
                'producto' => 'not_in:Elegir|required',
                'cantidad' => 'required|numeric|min:0|not_in:0',
                'precio'   => 'required']);
        }  
        $this->StoreOrUpdate($this->producto);  
    }
    public function StoreOrUpdate($id)
    {
        $this->productoFinalId = $id;    //lo uso para actualizar las facturas abiertas o pendientes
                                         //que contengan a este producto en sus detalles
        $this->totalAgrabar = $this->total + ($this->cantidad * $this->precio);
 
        DB::begintransaction();                         //iniciar transacción para grabar
        try{  
            if($this->selected_id > 0) {                //modifica
                $record = Detcompra::find($this->selected_id);  //actualizamos cantidad y/o precio
                $cantidad_detalle = $record->cantidad;         
                $record->update([
                    'cantidad' => $this->cantidad,
                    'precio'   => $this->precio
                ]);
            }else {                          //crea
                if($this->inicio_factura) {
                    if($this->proveedor == 'Elegir') $this->proveedor = null;                
                    $factura = Compra::create([
                        'letra'        => $this->letra,
                        'sucursal'     => $this->sucursal,
                        'num_fact'     => $this->numFact,
                        'proveedor_id' => $this->proveedor,
                        'importe'      => $this->totalAgrabar,
                        'estado'       => 'abierta',
                        'user_id'      => auth()->user()->id,
                        'comercio_id'  => $this->comercioId,
                        'fecha_fact'   => Carbon::parse($this->fecha)->format('Y,m,d H:i:s')
                    ]);
                    $this->inicio_factura = false;
                    $this->factura_id = $factura->id;
                }
                
                $record = Compra::find($this->factura_id);  //actualizamos el encabezado
                $record->update(['importe' => $this->totalAgrabar]);

                //CREA DETALLE               
                $existe = Detcompra::select('id')       //buscamos si ya está cargado
                        ->where('factura_id', $this->factura_id)
                        ->where('comercio_id', $this->comercioId)
                        ->where('producto_id', $id)->get(); 

                if ($existe->count()){  //si el item cargado ya existe en el detalle, anulamos
                                        //la carga y lo obligamos a que modifique el detalle mismo            
                    $this->emit('item_existente');
                    $this->resetinput();
                    return;
                }else{
                    //si es un producto, guardo su precio de costo antes de cargar el nuevo precio,
                    //por si acaso luego decido eliminar este item, en tal caso deberé calcular todos los
                    //precios con el valor anterior
               
                        $historico = Producto::find($id);
                        $this->costo_historico        = $historico->precio_costo;
                        $this->venta_sug_l1_historico = $historico->precio_venta_sug_l1;
                        $this->venta_sug_l2_historico = $historico->precio_venta_sug_l2;
                        $this->venta_l1_historico     = $historico->precio_venta_l1;
                        $this->venta_l2_historico     = $historico->precio_venta_l2;
                        $buscar_historico = Historico::where('producto_id', $id)
                            ->where('comercio_id', $this->comercioId)->get();
                        if($buscar_historico->count()){
                            $buscar_historico[0]->update([
                                'precio_costo'        => $this->costo_historico,
                                'precio_venta_sug_l1' => $this->venta_sug_l1_historico,
                                'precio_venta_sug_l2' => $this->venta_sug_l2_historico,
                                'precio_venta_l1'     => $this->venta_l1_historico,
                                'precio_venta_l2'     => $this->venta_l2_historico
                            ]);
                        }else{
                            $add_precio_historico = Historico::create([
                            'producto_id'         => $id,
                            'precio_costo'        => $this->costo_historico,
                            'precio_venta_sug_l1' => $this->venta_sug_l1_historico,
                            'precio_venta_sug_l2' => $this->venta_sug_l2_historico,
                            'precio_venta_l1'     => $this->venta_l1_historico,
                            'precio_venta_l2'     => $this->venta_l2_historico,
                            'comercio_id'         => $this->comercioId
                            ]); 
                        }
                          
                        $add_item = DetCompra::create([         //creamos un nuevo detalle
                            'factura_id'      => $this->factura_id,
                            'producto_id'     => $id,
                            'cantidad'        => $this->cantidad,
                            'precio'          => $this->precio,
                            'precio_temporal' => $this->precio,
                            'comercio_id'     => $this->comercioId
                        ]); 
                }
            } 

            //GRABAR AL PROVEEDOR DE ESTE PRODUCTO
            $record = ProductoProveedor::where('producto_id', $id)
                ->where('proveedor_id', $this->proveedor)->first();
            if(!$record){
                ProductoProveedor::create([
                    'producto_id'  => $id,
                    'proveedor_id' => $this->proveedor,
                    'comercio_id'  => $this->comercioId
                ]);
            }
            
            //ACTUALIZO EL PRECIO DE COMPRA Y/O VENTA DEL PRODUCTO COMPRADO... O NO
            if($this->cambiar_precios <> 'no'){ 
                $record = Producto::find($id);
                if($this->cambiar_precios == 'solo_costos'){        //modifica precios sugeridos  
                    $record->update([
                        'precio_costo' 	        => $this->precio,		
                        'precio_venta_sug_l1'   => $this->precio_venta_sug_l1,
                        'precio_venta_sug_l2'   => $this->precio_venta_sug_l2
                    ]);
                }elseif($this->cambiar_precios == 'cambiar_todo'){  //modifica todo
                    $record->update([
                        'precio_costo' 	        => $this->precio,			
                        'precio_venta_l1'       => $this->precio_venta_l1,
                        'precio_venta_l2'       => $this->precio_venta_l2,	
                        'precio_venta_sug_l1'   => $this->precio_venta_sug_l1,
                        'precio_venta_sug_l2'   => $this->precio_venta_sug_l2
                    ]);
                }                                                   //sino, no modifica nada

                //ACTUALIZO LOS ARTICULOS CON RECETA QUE CONTENGAN ESTE PRODUCTO COMO MATERIA PRIMA.
                //PARA ELLO DEBO CALCULAR EL TOTAL DE LA RECETA TENIENDO ESPECIAL ATENCIÓN AL VALOR DE LA
                //MATERIA PRIMA QUE ESTAMOS CARGANDO PARA UTILIZARLO EN DICHO CÁLCULO
                $record = DetReceta::join('productos as p', 'p.id', 'det_recetas.producto_id')
                    ->where('det_recetas.producto_id', $id)
                    ->where('det_recetas.comercio_id', $this->comercioId)
                    ->select('det_recetas.receta_id')->get();
                if($record){    //recetas con el producto como materia prima
                    foreach ($record as $i) {
                        $total_costo_receta = 0;  
                        $total = DetReceta::join('productos as p', 'p.id', 'det_recetas.producto_id')
                            ->where('det_recetas.receta_id', $i->receta_id)
                            ->where('det_recetas.comercio_id', $this->comercioId)
                            ->select('det_recetas.*', 'p.precio_costo')->get();
                        if($total){                  //calculo el costo total de la receta para modificar 
                            foreach ($total as $j) { //luego el costo del producto de venta
                                if($j->producto_id == $id) $total_costo_receta += $j->cantidad * $this->precio;
                                else $total_costo_receta += $j->cantidad * $j->precio_costo; 
                            }
                        }
                        //RECUPERO DATOS DEL PRODUCTO FINAL DE LA RECETA 
                        $data_producto_receta = Receta::join('productos as p', 'p.id', 'recetas.producto_id')
                            ->join('categorias as c', 'c.id', 'p.categoria_id')
                            ->where('recetas.id', $i->receta_id)                      
                            ->where('recetas.comercio_id', $this->comercioId)
                            ->select('p.id', 'c.margen_1', 'c.margen_2')->get();

                        $this->precio = $total_costo_receta;   
                        if ($this->calcular_precio_de_venta == 0){
                            //calcula el precio de venta sumando el margen de ganancia al costo del producto
                            $this->precio_venta_sug_l1 = ($this->precio * $data_producto_receta[0]->margen_1) / 100 + $this->precio;
                            $this->precio_venta_sug_l2 = ($this->precio * $data_producto_receta[0]->margen_2) / 100 + $this->precio;
                            $this->precio_venta_l1 = ($this->precio * $data_producto_receta[0]->margen_1) / 100 + $this->precio;
                            $this->precio_venta_l2 = ($this->precio * $data_producto_receta[0]->margen_2) / 100 + $this->precio;
                        
                        }else{
                            //calcula el precio de venta obteniendo el margen de ganancia sobre el mismo
                            $this->precio_venta_sug_l1 = $this->precio * 100 / (100 - $data_producto_receta[0]->margen_1);
                            $this->precio_venta_sug_l2 = $this->precio * 100 / (100 - $data_producto_receta[0]->margen_2);
                            $this->precio_venta_l1 = $this->precio * 100 / (100 - $data_producto_receta[0]->margen_1);
                            $this->precio_venta_l2 = $this->precio * 100 / (100 - $data_producto_receta[0]->margen_2);
                        }
                        if ($this->redondear_precio_de_venta == 1){
                            $this->precio_venta_sug_l1 = round($this->precio_venta_sug_l1);
                            $this->precio_venta_sug_l2 = round($this->precio_venta_sug_l2);
                            $this->precio_venta_l1 = round($this->precio_venta_l1);
                            $this->precio_venta_l2 = round($this->precio_venta_l2);
                        }
                        //modifico los datos del PRODUCTO FINAL de la receta
                        $record = Producto::find($data_producto_receta[0]->id);
                        if($this->cambiar_precios == 'solo_costos'){   //modifica precios sugeridos  
                            $record->update([
                                'precio_costo' 	      => $this->precio,		
                                'precio_venta_sug_l1' => $this->precio_venta_sug_l1,
                                'precio_venta_sug_l2' => $this->precio_venta_sug_l2
                            ]);
                        }else{       				//modifica todo
                            $record->update([
                                'precio_costo' 	      => $this->precio,			
                                'precio_venta_l1'     => $this->precio_venta_l1,
                                'precio_venta_l2'     => $this->precio_venta_l2,	
                                'precio_venta_sug_l1' => $this->precio_venta_sug_l1,
                                'precio_venta_sug_l2' => $this->precio_venta_sug_l2
                            ]);
                        }                         
                    }
                }
                //VERIFICO LOS DETALLES DE FACTURA ABIERTA O PENDIENTE QUE CONTENGAN AL PRODUCTO
				//QUE ESTAMOS MODIFICANDO PARA LUEGO PREGUNTAR SI LOS QUIEREN MODIFICAR O NO
				$this->detalleProductoCargado = Factura::join('detfacturas as df', 'df.factura_id', 'facturas.id')
                    ->where('facturas.estado', 'abierta')
                    ->where('facturas.comercio_id', $this->comercioId)
                    ->where('df.producto_id', $id)
                    ->orWhere('facturas.estado', 'pendiente')
                    ->where('facturas.comercio_id', $this->comercioId)
                    ->where('df.producto_id', $id)
                    ->select('df.id', 'df.precio')->get();
            }           
            DB::commit();
            if($this->selected_id > 0){		
                session()->flash('message', 'Registro Actualizado');       
            }else{ 
                session()->flash('message', 'Registro Creado'); 
            }           
        }catch (\Exception $e){
            DB::rollback(); 
            session()->flash('message', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }     
        if($this->detalleProductoCargado && $this->detalleProductoCargado->count()){
			$this->emit('cambiarPrecioDetalle', $this->detalleProductoCargado->count(), 'agregar');
		}else{
			$this->resetInput();
			return;
		}
    }
    public function actualizarPreciosCargados($accion)
	{
		//ACTUALIZO LOS IMPORTES QUE FIGUREN EN LOS DETALLE DE FACTURA ABIERTA O PENDIENTE
		//EN DONDE CONTENGAN AL PRODUCTO QUE ESTAMOS MODIFICANDO
		DB::begintransaction();
        try{
			$detalle = Factura::join('detfacturas as df', 'df.factura_id', 'facturas.id')
				->where('facturas.estado', 'abierta')
				->where('facturas.comercio_id', $this->comercioId)
				->where('df.producto_id', $this->productoFinalId)
				->orWhere('facturas.estado', 'pendiente')
				->where('facturas.comercio_id', $this->comercioId)
				->where('df.producto_id', $this->productoFinalId)
				->select('facturas.mesa_id', 'df.id', 'df.precio')->get();
			if($detalle->count()){
				foreach ($detalle as $i) {
					$grabar = Detfactura::find($i->id);
                    if($accion == 'agregar'){
                        if($i->mesa_id) $grabar->update(['precio' => $this->precio_venta_l1]);
					    else $grabar->update(['precio' => $this->precio_venta_l2]);
                    }else{
                        if($i->mesa_id) $grabar->update(['precio' => $this->venta_l1_historico]);
					    else $grabar->update(['precio' => $this->venta_l2_historico]);
                    }					
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
    public function opcionCambiarPrecios($cambiar_precios)
    {
        $this->cambiar_precios = $cambiar_precios;
    }
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
    public function anularFactura($id, $comentario)
    {
        if ($id) {
            DB::begintransaction();
            try{
                $factura = Compra::find($id);
                $factura->update([ 'estado' => 'anulado']);
                $factura->delete();

                $audit = Auditoria::create([
                    'item_deleted_id' => $id,
                    'tabla'           => 'Compras',
                    'estado'          => '0',
                    'user_delete_id'  => auth()->user()->id,
                    'comentario'      => $comentario,
                    'comercio_id'     => $this->comercioId
                ]);
                session()->flash('msg-ok', 'Registro Anulado con éxito!!');
                DB::commit();               
            }catch (\Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se anuló...');
            }
            $this->resetInputTodos();
            return;
        }
    }
    public function elegirFormaDePago()
    {
        if($this->proveedor != ''){
            $cli = Proveedor::where('id', $this->proveedor)->get();
            $this->nomCli = $cli[0]->nombre_empresa;
        }
        $this->f_de_pago = '1';        
        $this->doAction(2);
    } 
    public function factura_contado()
    {
        DB::begintransaction();                      
        try{
            $record = Compra::find($this->factura_id);
            $record->update([
                'estado'        => 'contado',
                'estado_pago'   => '1',
                'importe'       => $this->total,
                'forma_de_pago' => $this->f_de_pago,
                'nro_comp_pago' => $this->nro_comp_pago,  
                'mercadopago'   => $this->mercadopago,
                'comentario'    => $this->comentarioPago
            ]);
            //ACTUALIZO TABLA PEPS
            $det_compra = Detcompra::where('factura_id', $this->factura_id)
                ->join('productos as p', 'p.id', 'det_compras.producto_id')
                ->select('det_compras.*', 'p.controlar_stock')->get();
            if ($det_compra->count() > 0) {
                foreach ($det_compra as $i) {
                    if ($i->controlar_stock == 'si') {
                        //(accion,agregarVenta,inicioFactura,detalleCompraId,detalleVentaId,cantidad,productoId,costoHistorico,stockActual)
                        $peps = $this->actualizarStockTrait(2, false, true, $i->id, null, $i->cantidad, $i->producto_id, $i->precio, null);
                    }
                }
            }
            if($peps) {
                DB::commit();
                $this->emit('facturaCobrada');
            } else {
                $this->emit('errorAlGrabarStock');
            }             
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }              
        $this->resetInputTodos();
    }        
    public function cuenta_corriente()
    {
        if($this->total == 0){
            session()->flash('msg-error', 'Compra vacía...'); 
        }else{
            $record = Compra::find($this->factura_id);
            $record->update([
                'estado' => 'ctacte',
                'importe' => $this->total
            ]);
            //ACTUALIZO TABLA PEPS
            $det_compra = Detcompra::where('factura_id', $this->factura_id)
                ->join('productos as p', 'p.id', 'det_compras.producto_id')
                ->select('det_compras.*', 'p.controlar_stock')->get();
            if ($det_compra->count() > 0) {
                foreach ($det_compra as $i) {
                    if ($i->controlar_stock == 'si') {
                        //(accion,agregarVenta,inicioFactura,detalleCompraId,detalleVentaId,cantidad,productoId,costoHistorico,stockActual)
                        $this->actualizarStockTrait(2, false, true, $i->id, null, $i->cantidad, $i->producto_id, $i->precio, null);
                    }
                }
            }              
            session()->flash('message', 'Compra enviada a Cuenta Corriente'); 
            $this->resetInputTodos();
        }
    }
    public function destroy($id, $esProducto) //eliminar / delete / remove
    {
        if($id){
            DB::begintransaction();                        
            try{     
                $record = Detcompra::find($id);
                $productoId = $record->producto_id;
                $cantidad = $record->cantidad;
                $precio = $record->precio;
                $importe_a_descontar = $cantidad * $precio;
                $record->delete(); 

                $this->totalAgrabar = $this->total - $importe_a_descontar;
                $record = Compra::find($this->factura_id);  //actualizamos el encabezado
                $record->update(['importe' => $this->totalAgrabar]); 

                $this->productoFinalId = $productoId;  //TOMO ESTE DATO PARA ACTUALIZAR LAS FACTURAS ABIERTAS

                //ACTUALIZO STOCK
                $record = Stock::where('producto_id', $productoId)->first();   
                $stockAnterior = $record['stock_actual'];
                $stockNuevo = $stockAnterior - $cantidad;  
                $record->update(['stock_actual' => $stockNuevo]);

        
                //BUSCO LOS PRECIOS ANTES DE LA CARGA DEL ITEM
                $historico = Historico::where('producto_id', $productoId)
                    ->where('comercio_id', $this->comercioId)->get();
                $this->costo_historico        = $historico[0]->precio_costo;
                $this->venta_sug_l1_historico = $historico[0]->precio_venta_sug_l1;
                $this->venta_sug_l2_historico = $historico[0]->precio_venta_sug_l2;
                $this->venta_l1_historico     = $historico[0]->precio_venta_l1;
                $this->venta_l2_historico     = $historico[0]->precio_venta_l2;

                //ACTUALIZO EL PRECIO DE COMPRA Y/O VENTA DEL PRODUCTO COMPRADO... O NO
                if($this->cambiar_precios <> 'no'){       
                    $record = Producto::find($productoId);
                    if($this->cambiar_precios == 'solo_costos'){        //modifica precios sugeridos  
                        $record->update([
                            'precio_costo' 	        => $this->costo_historico,		
                            'precio_venta_sug_l1'   => $this->venta_sug_l1_historico,
                            'precio_venta_sug_l2'   => $this->venta_sug_l2_historico
                        ]);
                    }elseif($this->cambiar_precios == 'cambiar_todo'){  //modifica todo
                        $record->update([
                            'precio_costo' 	        => $this->costo_historico,			
                            'precio_venta_l1'       => $this->venta_l1_historico,
                            'precio_venta_l2'       => $this->venta_l2_historico,	
                            'precio_venta_sug_l1'   => $this->venta_sug_l1_historico,
                            'precio_venta_sug_l2'   => $this->venta_sug_l2_historico
                        ]);
                    }                                                   //sino, no modifica nada

                    //ACTUALIZO LOS ARTICULOS CON RECETA QUE CONTENGAN ESTE PRODUCTO COMO MATERIA PRIMA.
                    //PARA ELLO DEBO CALCULAR EL TOTAL DE LA RECETA TENIENDO ESPECIAL ATENCIÓN AL VALOR DE LA
                    //MATERIA PRIMA QUE ESTAMOS CARGANDO PARA UTILIZARLO EN DICHO CÁLCULO
                    $record = DetReceta::join('productos as p', 'p.id', 'det_recetas.producto_id')
                        ->where('det_recetas.producto_id', $productoId)
                        ->where('det_recetas.comercio_id', $this->comercioId)
                        ->select('det_recetas.receta_id')->get();
                    if($record){    //recetas con el producto como materia prima
                        foreach ($record as $i) {
                            $total_costo_receta = 0;  
                            $total = DetReceta::join('productos as p', 'p.id', 'det_recetas.producto_id')
                                ->where('det_recetas.receta_id', $i->receta_id)
                                ->where('det_recetas.comercio_id', $this->comercioId)
                                ->select('det_recetas.*', 'p.precio_costo')->get();
                            if($total){                  //calculo el costo total de la receta para modificar 
                                foreach ($total as $j) { //luego el costo del producto de venta
                                    if($j->producto_id == $id) $total_costo_receta += $j->cantidad * $this->costo_historico;
                                    else $total_costo_receta += $j->cantidad * $j->precio_costo; 
                                }
                            }
                            //RECUPERO DATOS DEL PRODUCTO DE LA RECETA PARA ACTUALIZARLOS 
                            $data_producto_receta = Receta::join('productos as p', 'p.id', 'recetas.producto_id')
                                ->join('categorias as c', 'c.id', 'p.categoria_id')
                                ->where('recetas.id', $i->receta_id)                      
                                ->where('recetas.comercio_id', $this->comercioId)
                                ->select('p.id', 'c.margen_1', 'c.margen_2')->get();

                            $this->costo_historico = $total_costo_receta;   
                            if ($this->calcular_precio_de_venta == 0){
                                //calcula el precio de venta sumando el margen de ganancia al costo del producto
                                $this->venta_sug_l1_historico = ($this->costo_historico * $data_producto_receta[0]->margen_1) / 100 + $this->costo_historico;
                                $this->venta_sug_l2_historico = ($this->costo_historico * $data_producto_receta[0]->margen_2) / 100 + $this->costo_historico;
                                $this->venta_l1_historico     = ($this->costo_historico * $data_producto_receta[0]->margen_1) / 100 + $this->costo_historico;
                                $this->venta_l2_historico     = ($this->costo_historico * $data_producto_receta[0]->margen_2) / 100 + $this->costo_historico;
                            
                            }else{
                                //calcula el precio de venta obteniendo el margen de ganancia sobre el mismo
                                $this->venta_sug_l1_historico = $this->costo_historico * 100 / (100 - $data_producto_receta[0]->margen_1);
                                $this->venta_sug_l2_historico = $this->costo_historico * 100 / (100 - $data_producto_receta[0]->margen_2);
                                $this->venta_l1_historico     = $this->costo_historico * 100 / (100 - $data_producto_receta[0]->margen_1);
                                $this->venta_l2_historico     = $this->costo_historico * 100 / (100 - $data_producto_receta[0]->margen_2);
                            }
                            if ($this->redondear_precio_de_venta == 1){
                                $this->venta_sug_l1_historico = round($this->venta_sug_l1_historico);
                                $this->venta_sug_l2_historico = round($this->venta_sug_l2_historico);
                                $this->venta_l1_historico     = round($this->venta_l1_historico);
                                $this->venta_l2_historico     = round($this->venta_l2_historico);
                            }
                            //MODIFICO LOS DATOS DEL PRODUCTO FINAL DE LA RECETA
                            $record = Producto::find($data_producto_receta[0]->id);
                            if($this->cambiar_precios == 'solo_costos'){   //modifica precios sugeridos  
                                $record->update([
                                    'precio_costo' 	      => $this->costo_historico,		
                                    'precio_venta_sug_l1' => $this->venta_sug_l1_historico,
                                    'precio_venta_sug_l2' => $this->venta_sug_l2_historico
                                ]);
                            }else{       				//modifica todo
                                $record->update([
                                    'precio_costo' 	      => $this->costo_historico,			
                                    'precio_venta_l1'     => $this->venta_l1_historico,
                                    'precio_venta_l2'     => $this->venta_l2_historico,	
                                    'precio_venta_sug_l1' => $this->venta_sug_l1_historico,
                                    'precio_venta_sug_l2' => $this->venta_sug_l2_historico
                                ]);
                            }                         
                        }
                    }
                    if($this->cambiar_precios == 'cambiar_todo'){                        
                        //VERIFICO LOS DETALLES DE FACTURA ABIERTA O PENDIENTE QUE CONTENGAN AL PRODUCTO
                        //QUE ESTAMOS MODIFICANDO PARA LUEGO PREGUNTAR SI LOS QUIEREN MODIFICAR O NO
                        $this->detalleProductoCargado = Factura::join('detfacturas as df', 'df.factura_id', 'facturas.id')
                            ->where('facturas.estado', 'abierta')
                            ->where('facturas.comercio_id', $this->comercioId)
                            ->where('df.producto_id', $this->productoFinalId)
                            ->orWhere('facturas.estado', 'pendiente')
                            ->where('facturas.comercio_id', $this->comercioId)
                            ->where('df.producto_id', $this->productoFinalId)
                            ->select('df.id', 'df.precio')->get();
                    }
                }
                DB::commit();
                session()->flash('message', 'Registro Eliminado'); 
            }catch (Exception $e){
                DB::rollback(); 
                session()->flash('message', '¡¡¡ATENCIÓN!!! El registro no se eliminó...');
            }     
            if($this->detalleProductoCargado && $this->detalleProductoCargado->count()){
                $this->emit('cambiarPrecioDetalle', $this->detalleProductoCargado->count(), '');
            }else{
                $this->resetInput();
                return;
            } 
        }
    }
}
