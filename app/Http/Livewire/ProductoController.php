<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Artisan;

use Livewire\Component;
use App\Traits\GenericTrait;
use App\Models\Auditoria;
use App\Models\Balance;
use App\Models\Categoria;
use App\Models\Comercio;
use App\Models\Detcompra;
use App\Models\DetReceta;
use App\Models\Detfactura;
use App\Models\Factura;
use App\Models\Peps;
use App\Models\Producto;
use App\Models\ProductoProveedor;
use App\Models\Proveedor;
use App\Models\Receta;
use App\Models\Rubro;
use App\Models\Stock;
use App\Models\Subproducto;
use App\Models\TextoBaseComanda;
use App\Models\TipoArticulo;
use App\Models\SectorComanda;
use DB;
Use Session;


class ProductoController extends Component
{
	use GenericTrait;

	public $categoria ='Elegir', $tipo = 'Elegir', $sector = 0, $texto = null, $estado='Disponible';
	public $proveedor ='Elegir', $presentacion, $unidad_de_medida = 'Elegir';
	public $codigo = null, $codigo_sugerido, $descripcion, $categorias = [], $proveedores, $producto;
	public $stock_actual = null, $stock_ideal = null, $stock_minimo = null, $merma;
	public $precio_costo, $precio_venta_l1, $precio_venta_l2, $precio_venta_sug_l1, $precio_venta_sug_l2;
	public $selected_id = null, $calcular_precio_de_venta, $redondear_precio_de_venta;
	public $recuperar_registro = 0, $descripcion_soft_deleted, $id_soft_deleted;
	public $action = 1, $search, $habilitar_modal = false;
	public $sectores, $se_imprime = 0, $textos, $tipos;
	public $salsa = false, $guarnicion = false, $tiene_receta = 'no', $controlar_stock = 'si';
	public $comercioId, $comercioTipo, $modComandas, $modDelivery, $descProducto; 
	public $info = [], $verDisponibles = true, $idProducto, $idProductoHistorial;
	public $cambiar_precios, $costo_actual, $detalleProductoCargado, $precio;
	public $idNuevoProducto, $descNuevoProducto, $stock_historico;
	public $infoHistorial = [], $infoProductoProveedor = [], $action_edit = 'datos', $nuevo_producto;

	public function mount()
	{
		//busca el comercio que está en sesión
		$this->comercioId = session('idComercio');
		$this->comercioTipo = session('tipoComercio');
		$this->modDelivery = session('modDelivery');
		$this->modComandas = session('modComandas');
        session(['facturaPendiente' => null]);	
		
		//$this->categorias = Categoria::select('*')->where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();
		$this->sectores = SectorComanda::select('*')->where('comercio_id', $this->comercioId)->get();
		$this->proveedores = Proveedor::select('id', 'nombre_empresa')->where('comercio_id', $this->comercioId)->orderBy('nombre_empresa')->get();
		$this->tipos = TipoArticulo::all();



		$record = Comercio::find($this->comercioId);
		$this->calcular_precio_de_venta = $record->calcular_precio_de_venta;
		$this->redondear_precio_de_venta = $record->redondear_precio_de_venta;
		if(!$this->cambiar_precios){
            if($record->opcion_de_guardado_producto == "1") $this->cambiar_precios = 'solo_costos';
            else $this->cambiar_precios = 'cambiar_todo';
        }
	}
	
	public function render()
	{
		//genero código autoincremental del producto nuevo
		$this->generar_codigo_de_producto();
	
		//mostrar productos
		$this->productos_list();

		if ($this->tipo != 'Elegir') {       
            $this->categorias = Categoria::where('tipo_id', $this->tipo)
                ->where('comercio_id', $this->comercioId)
				->select('id', 'descripcion')
				->orderBy('descripcion')->get();
        } else $this->categoria = 'Elegir';

		$this->textos = TextoBaseComanda::select('*')->where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();
		
		if($this->sector == 0) $this->se_imprime = 0; else $this->se_imprime = 1;

		return view('livewire.productos.component');
	}
	public function resetInput()  
	{  
		$this->info                 = [];
		$this->codigo               = null;
		$this->descripcion          = '';
		$this->nuevo_producto       = '';
		$this->presentacion         = '';
		$this->unidad_de_medida     = 'Elegir';
		$this->precio_costo         = null;
		$this->merma                = null;
		$this->precio_venta_l1      = null;
		$this->precio_venta_l2      = null;
		$this->precio_venta_sug_l1  = null;
		$this->precio_venta_sug_l2  = null;
		$this->stock_actual         = null; 
		$this->stock_ideal          = null; 
		$this->stock_minimo         = null;
		$this->tipo                 = 'Elegir';
		$this->categoria            = 'Elegir';
		$this->sector               = 0;
		$this->texto                = null;
		$this->salsa                = false;
		$this->estado               = 'Disponible';
		$this->selected_id          = null;
		$this->search               = '';
		$this->habilitar_modal      = false;
		$this->tiene_receta         = 'no';
		$this->controlar_stock      = 'si';
		$this->proveedor            = 'Elegir';
		$this->descProducto         = null;
		$this->costo_actual         = null;
		$this->verDisponibles       = true;
		$this->stock_historico      = null;
		$this->detalleProductoCargado = null;
		$this->action_edit          = 'datos';
		$this->emit('search_focus');   
	}	
	protected $listeners = [
		'deleteRow'                 => 'destroy',
		'deleteProductoProveedor'   => 'destroy_productoProveedor',
		'calcular_precio_venta',
		'validarProducto',
		'guardar'                   => 'StoreOrUpdate', 
		'grabar_texto_base', 
		'productoProveedor',
		'productoHistorialCompras',
		'grabarProductoProveedor',
		'opcionCambiarPrecios',
		'actualizarPreciosCargados',
		'ver_receta',      
		'cargarStockInicial',
		'validarStockNegativo',
    	'VerDisponibles',
		'action_edit',
		'doAction'
	];
	public function generar_codigo_de_producto()
	{
		if($this->selected_id == null) {
			$nuevo_codigo = Producto::select('*')->where('comercio_id', $this->comercioId)->get();
			if ($nuevo_codigo->count() == 0){
				$this->codigo_sugerido = 1;
			}else{
				$nuevo_codigo = Producto::select()
					->where('comercio_id', $this->comercioId)
					->orderBy('id','desc')->withTrashed()->first();
				$this->codigo_sugerido = $nuevo_codigo->codigo + 1;
			}
		}else{
			$this->codigo_sugerido = $this->selected_id;
		}
		if ($this->codigo == null) $this->codigo = $this->codigo_sugerido;
	} 
	public function productos_list()
	{
		if (!$this->verDisponibles) {
			$this->info = Producto::join('categorias as c','c.id','productos.categoria_id')
				->join('tipo_articulos as t','t.id','c.tipo_id')
				->select('productos.*', 't.descripcion as tipo', 'c.descripcion as categoria', 
					DB::RAW("0 as stock_actual"), DB::RAW("0 as proveedor"), DB::RAW("0 as historial"))
				->where('productos.comercio_id', $this->comercioId)
				->where('estado', 'Suspendido')
				->orderBy('productos.descripcion', 'asc')
				->get();
			$this->search = '';
		} else {
			if(strlen($this->search) > 0) {
				$this->info = Producto::join('categorias as c','c.id','productos.categoria_id')
					->join('tipo_articulos as t','t.id','c.tipo_id')
					->select('productos.*', 't.descripcion as tipo', 'c.descripcion as categoria', 
						DB::RAW("0 as stock_actual"), DB::RAW("0 as proveedor"), DB::RAW("0 as historial"))
					->where('productos.descripcion', 'like', '%' . $this->search .'%')
					->where('productos.comercio_id', $this->comercioId)
					->where('estado', 'Disponible')
					->orWhere('productos.estado', 'like', '%' . $this->search .'%')
					->where('productos.comercio_id', $this->comercioId)
					->where('estado', 'Disponible')
					->orWhere('t.descripcion', 'like', '%' . $this->search .'%')
					->where('productos.comercio_id', $this->comercioId)
					->where('estado', 'Disponible')
					->orWhere('c.descripcion', 'like', '%' . $this->search .'%')
					->where('productos.comercio_id', $this->comercioId)
					->where('estado', 'Disponible')
					->orWhere('productos.codigo', 'like', '%' . $this->search .'%')
					->where('productos.comercio_id', $this->comercioId)
					->where('estado', 'Disponible')
					->orderBy('productos.descripcion', 'asc')
					->get();
			}else {
				$this->info = Producto::join('categorias as c','c.id','productos.categoria_id')
					->join('tipo_articulos as t','t.id','c.tipo_id')
					->select('productos.*', 't.descripcion as tipo', 'c.descripcion as categoria', 
						DB::RAW("0 as stock_actual"), DB::RAW("0 as proveedor"), DB::RAW("0 as historial"))
					->where('productos.comercio_id', $this->comercioId)
					->where('estado', 'Disponible')
					->orderBy('productos.descripcion', 'asc')
					->get();
			}
		}
        foreach ($this->info as $i){
			$stock = Peps::where('producto_id', $i->id)->sum('resto');
			if($stock) $i->stock_actual = $stock;		
		
			$proveedor = ProductoProveedor::where('producto_id', $i->id)
				->where('comercio_id', $this->comercioId)->select('id')->get();
			if($proveedor->count()) $i->proveedor = 1;
			else $i->proveedor = 0;

			$historial = Detcompra::where('producto_id', $i->id)
				->where('comercio_id', $this->comercioId)->select('id')->get();
			if($historial->count()) $i->historial = 1;
			else $i->historial = 0;	
        }
	}
	public function action_edit($actionEdit)
	{
		$this->action_edit = $actionEdit;
	}
	public function opcionCambiarPrecios($cambiar_precios)
    {
        $this->cambiar_precios = $cambiar_precios;
		$this->calcular_precio_venta();
		
    }
	public function VerDisponibles($valor)
	{
		$this->verDisponibles = $valor;
	}
	public function ver_receta($id)
	{
		session(['producto_receta_id' => $id]); 
		return redirect()->to('/recetas');

	}
	public function doAction($action)
	{
		$this->action = $action;
		if ($this->action == 1)	$this->resetInput();
		//$this->ejecutar_command();
	}
	public function ejecutar_command()
	{
		Artisan::call('resumen:diario');
	}	
	public function edit($id)
	{
		$this->action = 2;
		$record = Producto::find($id);
		$this->selected_id         = $id;
		$this->categoria           = $record->categoria_id;		
		$this->codigo              = $record->codigo;
		$this->descripcion         = $record->descripcion;
		$this->presentacion        = $record->presentacion;
		$this->unidad_de_medida    = $record->unidad_de_medida;
		$this->precio_costo        = $record->precio_costo;
		$this->merma               = $record->merma;
		$this->precio_venta_l1     = $record->precio_venta_l1;
		$this->precio_venta_l2     = $record->precio_venta_l2;
		$this->precio_venta_sug_l1 = $record->precio_venta_sug_l1;
		$this->precio_venta_sug_l2 = $record->precio_venta_sug_l2;
		$this->stock_ideal         = $record->stock_ideal;
		$this->stock_minimo        = $record->stock_minimo;
		$this->tiene_receta        = $record->tiene_receta;
		$this->controlar_stock     = $record->controlar_stock;
		$this->estado              = $record->estado;
		$this->texto               = $record->texto_base_comanda_id;
		$this->costo_actual        = $record->precio_costo;
		if($record->sectorcomanda_id) $this->sector = $record->sectorcomanda_id;
		else $this->sector = 0;
		
		$stock_actual = Peps::where('producto_id', $id)->sum('resto');
		if($stock_actual){
			$this->stock_actual = $stock_actual;
			$this->stock_historico = $stock_actual;
		}

		$tipo = Categoria::find($this->categoria);
        $this->tipo = $tipo->tipo_id;

		if($record->guarnicion == 1) $this->guarnicion = true; else $this->guarnicion = false;
		if($record->salsa == 1) $this->salsa = true; else $this->salsa = false;
	}	
	public function volver($tipo)
    {
		$this->recuperar_registro = 0;
		if($tipo == 1) $this->resetInput();
		else $this->doAction(3);
        return; 
    }	
    public function RecuperarRegistro($id,$tipo)
    {
		DB::begintransaction();
        try{
			if($tipo == 1){ //producto
				Producto::onlyTrashed()->find($id)->restore();
				$audit = Auditoria::create([
					'item_deleted_id' => $id,
					'tabla'           => 'Productos',
					'estado'          => '1',
					'user_delete_id'  => auth()->user()->id,
					'comentario'      => '',
					'comercio_id'     => $this->comercioId
				]);
			}else{       //subproducto
				Subproducto::onlyTrashed()->find($id)->restore();
				$audit = Auditoria::create([
					'item_deleted_id' => $id,
					'tabla'           => 'Subproductos',
					'estado'          => '1',
					'user_delete_id'  => auth()->user()->id,
					'comentario'      => '',
					'comercio_id'     => $this->comercioId
				]);
			}
			session()->flash('msg-ok', 'Registro recuperado');
            $this->volver($tipo);
            
            DB::commit();               
        }catch (\Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se recuperó...');
        }
    }
	public function grabar_texto_base($texto)
	{
		$this->texto = ucwords($texto);
        $this->validate([
            'texto' => 'required'
        ]);
        DB::begintransaction();
        try{
			$existe = TextoBaseComanda::where('descripcion', $this->texto)
				->where('comercio_id', $this->comercioId)->withTrashed()->get();
				if($existe->count()) {
				$this->emit('texto_existe');
				return;
			}
			$texto = TextoBaseComanda::create([
				'descripcion' => ucwords($this->texto), 
				'comercio_id' => $this->comercioId            
			]);
			$this->texto = $texto->id;	
			$this->emit('texto_creado');  
            DB::commit(); 
        }catch (\Exception $e){
            DB::rollback();
			$this->emit('registro_no_grabado');
        }
		return;
	}
	public function StoreOrUpdate($salsa, $guarnicion, $solo_precios_listas)
	{
		if ($this->validaciones());

		$verificar_stock = true;
		$actualizarStock = false;
	
		DB::begintransaction();
        try{
			if($this->selected_id != null) {
				$diferenciaDeStock = $this->stock_actual - $this->stock_historico;
				if ($diferenciaDeStock > 0 && $this->tiene_receta == 'si') {
					$verificar_stock = $this->verificar_stock_receta($this->selected_id, $diferenciaDeStock);
				}
				$actualizarStock = $this->product_update($salsa, $guarnicion, $solo_precios_listas);				
			}else $actualizarStock = $this->product_create($salsa, $guarnicion);	

            //verifico si se grabaron todos los datos de stock
            if ($actualizarStock && $verificar_stock){
                DB::commit();
				if($this->selected_id > 0) session()->flash('msg-ok', 'Producto Actualizado');       
                else session()->flash('msg-ok', 'Producto Creado');
            } else {
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó porque existen problemas al actualizar el Stock...');
            }                
		}catch (Exception $e){
			DB::rollback();
			if($e->getCode() == '22003') session()->flash('msg-error', 'Valor numérico fuera de rango');
			else session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
		}

		if($this->selected_id === null && $this->tiene_receta == 'si') $this->emit('crear_receta',$this->idNuevoProducto,$this->descNuevoProducto);

		if($this->detalleProductoCargado && $this->detalleProductoCargado->count() > 0) {
			$this->emit('cambiarPrecioDetalle', $this->detalleProductoCargado->count());
		}
		$this->doAction(1);	
	}

	public function product_update($salsa, $guarnicion, $solo_precios_listas)
	{
		$record = Producto::find($this->selected_id);
		if($solo_precios_listas == 1){   //si no modifico el precio de costo
			$record->update([
				'descripcion'  	        => ucwords($this->descripcion),
				'merma' 	            => $this->merma,
				'precio_venta_l1'       => $this->precio_venta_l1,
				'precio_venta_l2'       => $this->precio_venta_l2,
				'presentacion'          => $this->presentacion,
				'unidad_de_medida'	    => $this->unidad_de_medida,
				'tiene_receta'          => $this->tiene_receta,
				'controlar_stock'       => $this->controlar_stock,
				'stock_ideal'           => $this->stock_ideal,
				'stock_minimo'          => $this->stock_minimo,
				'categoria_id' 	        => $this->categoria,
				'estado'       	        => $this->estado,
				'salsa'                 => $salsa,
				'guarnicion'            => $guarnicion,
				'sectorcomanda_id'      => $this->sector,
				'texto_base_comanda_id' => $this->texto
			]);
		}else{             //modifico el precio de costo					
			if($this->cambiar_precios == 'solo_costos'){  //modifica solo costo y precios sugeridos  
				$record->update([
					'descripcion'  	        => ucwords($this->descripcion),
					'precio_costo' 	        => $this->precio_costo,	
					'merma' 	            => $this->merma,	
					'precio_venta_sug_l1'   => $this->precio_venta_sug_l1,
					'precio_venta_sug_l2'   => $this->precio_venta_sug_l2,	
					'presentacion'          => $this->presentacion,
					'unidad_de_medida'	    => $this->unidad_de_medida,
					'tiene_receta'          => $this->tiene_receta,
					'controlar_stock'       => $this->controlar_stock,
					'stock_ideal'           => $this->stock_ideal,
					'stock_minimo'          => $this->stock_minimo,
					'categoria_id' 	        => $this->categoria,
					'estado'       	        => $this->estado,
					'salsa'                 => $salsa,
					'guarnicion'            => $guarnicion,
					'sectorcomanda_id'      => $this->sector,
					'texto_base_comanda_id' => $this->texto
				]);
			}elseif($this->cambiar_precios == 'cambiar_todo'){  //modifica todo
				$record->update([
					'descripcion'  	        => ucwords($this->descripcion),
					'precio_costo' 	        => $this->precio_costo,	
					'merma' 	            => $this->merma,		
					'precio_venta_l1'       => $this->precio_venta_l1,
					'precio_venta_l2'       => $this->precio_venta_l2,	
					'precio_venta_sug_l1'   => $this->precio_venta_sug_l1,
					'precio_venta_sug_l2'   => $this->precio_venta_sug_l2,	
					'presentacion'          => $this->presentacion,
					'unidad_de_medida'	    => $this->unidad_de_medida,
					'tiene_receta'          => $this->tiene_receta,
					'controlar_stock'       => $this->controlar_stock,
					'stock_ideal'           => $this->stock_ideal,
					'stock_minimo'          => $this->stock_minimo,
					'categoria_id' 	        => $this->categoria,
					'estado'       	        => $this->estado,
					'salsa'                 => $salsa,
					'guarnicion'            => $guarnicion,
					'sectorcomanda_id'      => $this->sector,
					'texto_base_comanda_id' => $this->texto
				]);
			}
		}

		//$this->actualizarRecetasRelacionadas($this->selected_id);
		
		if($this->cambiar_precios == 'cambiar_todo'){
			//VERIFICO LOS DETALLES DE FACTURA ABIERTA O PENDIENTE QUE CONTENGAN AL PRODUCTO
			//QUE ESTAMOS MODIFICANDO PARA LUEGO PREGUNTAR SI LOS QUIEREN MODIFICAR O NO
			$this->detalleProductoCargado = Factura::join('detfacturas as df', 'df.factura_id', 'facturas.id')
				->where('facturas.estado', 'abierta')
				->where('facturas.comercio_id', $this->comercioId)
				->where('df.producto_id', $this->selected_id)
				->orWhere('facturas.estado', 'pendiente')
				->where('facturas.comercio_id', $this->comercioId)
				->where('df.producto_id', $this->selected_id)
				->select('df.id', 'df.precio')->get();			
		}

		$actualizarStock = true;
		//llamando al Trait para modificar STOCK solo si se modificó el Stock Actual
		if ($this->stock_historico != $this->stock_actual) {
			if ($this->controlar_stock == 'si') {
				$actualizarStock = $this->actualizarStockTrait(4, false, false, null, null, 
					null, $this->selected_id, $this->precio_costo, $this->stock_actual);						
			}
		}
		return $actualizarStock;
	}
	public function product_create($salsa, $guarnicion)
	{
		$producto = Producto::create([
			'codigo'                => $this->codigo_sugerido,
			'descripcion'           => ucwords($this->descripcion),
			'precio_costo'          => $this->precio_costo,
			'merma' 	            => $this->merma,
			'precio_venta_l1'       => $this->precio_venta_l1,
			'precio_venta_l2'       => $this->precio_venta_l2,
			'precio_venta_sug_l1'   => $this->precio_venta_sug_l1,
			'precio_venta_sug_l2'   => $this->precio_venta_sug_l2,
			'presentacion'          => $this->presentacion,
			'unidad_de_medida'	    => $this->unidad_de_medida,
			'tiene_receta'          => $this->tiene_receta,
			'controlar_stock'       => $this->controlar_stock,
			'stock_ideal'           => $this->stock_ideal,
			'stock_minimo'          => $this->stock_minimo,
			'categoria_id'          => $this->categoria,
			'estado'                => $this->estado,
			'comercio_id'           => $this->comercioId,
			'salsa'                 => $salsa,
			'guarnicion'            => $guarnicion,
			'sectorcomanda_id'      => $this->sector,
			'texto_base_comanda_id' => $this->texto
		]);
		
		$this->descNuevoProducto = $producto->descripcion;
		$this->idNuevoProducto = $producto->id;

		//STOCK
		$actualizarStock = false;
		$actualizarStock = $this->actualizarStockTrait(1, false, false, null, null, null, $producto->id, $this->precio_costo, $this->stock_actual);	

		return $actualizarStock;
	}
	public function validaciones()
	{
		if($this->tipo == 3 || $this->tipo == 4) $this->tiene_receta = 'si';
		else $this->tiene_receta = 'no';
	
        if(!$this->precio_costo){  		//si creo un art. de venta c/receta o un art. elaborado
			$this->precio_costo = 0;	//no tendrá precios de costo y de venta hasta que elaboremos
			$this->merma = 0;			//su receta
			$this->precio_venta_l1 = 0;
			$this->precio_venta_l2 = 0;
			$this->precio_venta_sug_l1 = 0;
			$this->precio_venta_sug_l2 = 0;
		}

		if ($this->tipo == 1) {	 //si es un art. de compra no necesito precios de venta
			$this->precio_venta_l1 = 0;
			$this->precio_venta_l2 = 0;
			$this->precio_venta_sug_l1= 0; 
			$this->precio_venta_sug_l2= 0; 
		} 

		if(!$this->merma) $this->merma = 0;

		if($this->modComandas != "1"){
			$this->sector = null;
			$this->texto = null;
		}
		if($this->sector == 0){
			$this->sector = null;
			$this->texto = null;
		}
	
		if($this->sector != null){
			$this->validate(
				['texto' => 'required'],
				['texto.required' => 'Debe agregar un texto base para la comanda']);
		}
		
		if ($this->tiene_receta == 'no') {
			$this->validate(
				['precio_costo' => 'required|not_in:0'],
				['precio_costo.not_in' => 'El Precio de Costo debe ser mayor a 0']);
		}
		
		if($this->tiene_receta == 'no' && $this->tipo == 2){
			$this->validate([
				'precio_venta_l1' => 'required'],
				['precio_venta_l1.required' => 'El campo Pr/Vta Salón (Lista) es obligatorio.']);
		} 
	
		$this->validate(['categoria' => 'not_in:Elegir', 'unidad_de_medida' => 'not_in:Elegir']);

		$this->validate([
				'descripcion'  => 'required',
				'presentacion' => 'required',
				'estado'       => 'required',
				'tipo'         => 'required'],
            ['descripcion.required' => 'El Nombre del Producto no puede estar vacío']);

		$this->validate(
			['presentacion' => 'not_in:0'],
			['presentacion.not_in' => 'El valor de Presentación debe ser mayor a 0']);

		if($this->stock_actual == '') $this->stock_actual = null;
		if($this->stock_ideal == '') $this->stock_ideal = null;
		if($this->stock_minimo == '') $this->stock_minimo = null;

		$this->descripcion = ucwords($this->descripcion);

		$this->validarCodigo();
	}
	public function validarProducto()
	{
		if($this->selected_id != null) {
			$existe = Producto::where('descripcion', $this->descripcion)
				->where('id', '<>', $this->selected_id)
				->where('comercio_id', $this->comercioId)
				->withTrashed()->get();
			if($existe->count() && $existe[0]->deleted_at != null) {
				session()->flash('info', 'El Producto que desea crear ya existe pero fué eliminado anteriormente, para recuperarlo haga click en el botón "Recuperar registro"');
				$this->action = 1;
				$this->recuperar_registro = 1;
				$this->descripcion_soft_deleted = $existe[0]->descripcion;
				$this->id_soft_deleted = $existe[0]->id;
				return;
			}elseif($existe->count()) {
				$this->emit('registroRepetido');
				return;
			}else {
				$this->nuevo_producto = $this->descripcion;
				$this->habilitar_modal = true; //habilito la presentación del model para agregar Texto Base
			}
		}else{
			$existeProducto = Producto::where('descripcion', $this->descripcion)
				->where('comercio_id', $this->comercioId)->withTrashed()->get();
			if($existeProducto->count() && $existeProducto[0]->deleted_at != null) {
				session()->flash('info', 'El Producto que desea agregar fué eliminado anteriormente, para recuperarlo haga click en el botón "Recuperar registro"');
				$this->action = 1;
				$this->recuperar_registro = 1;
				$this->descripcion_soft_deleted = $existeProducto[0]->descripcion;
				$this->id_soft_deleted = $existeProducto[0]->id;
				return;
			}elseif($existeProducto->count()) {
				Session::flash('info', 'El Producto ya existe...');
				$this->resetInput();
				return;
			}else {
				$this->nuevo_producto = $this->descripcion;
				$this->habilitar_modal = true; //habilito la presentación del model para agregar Texto Base
			}
		}
	}
	public function validarCodigo()
	{
		if($this->selected_id != null) {
				//BUSCO SI EL CÓDIGO DEL PRODUCTO YA EXISTE
				$existeCodigo = Producto::where('codigo', $this->codigo)
				->where('id', '<>', $this->selected_id)
				->where('comercio_id', $this->comercioId)
				->withTrashed()->get();
			if($existeCodigo->count() && $existeCodigo[0]->deleted_at != null) {
				session()->flash('info', 'El Código que desea modificar ya existe pero fué eliminado anteriormente, para recuperarlo haga click en el botón "Recuperar registro"');
				$this->action = 1;
				$this->recuperar_registro = 1;
				$this->descripcion_soft_deleted = $existeCodigo[0]->descripcion;
				$this->id_soft_deleted = $existeCodigo[0]->id;
				return;				
			}elseif($existeCodigo->count()) {
				session()->flash('info', 'El Código ya existe...');
				return;
			}
		} else {
			$existeCodigo = Producto::where('codigo', $this->codigo)
				->where('comercio_id', $this->comercioId)		
				->withTrashed()->get();
			if($existeCodigo->count() && $existeCodigo[0]->deleted_at != null) {
				session()->flash('info', 'El Código que desea agregar ya existe pero fué eliminado anteriormente, para recuperarlo haga click en el botón "Recuperar registro"');
				$this->action = 1;
				$this->recuperar_registro = 1;
				$this->descripcion_soft_deleted = $existeCodigo[0]->descripcion;
				$this->id_soft_deleted = $existeCodigo[0]->id;
				return;				
			}elseif($existeCodigo->count()) {
				session()->flash('info', 'El Código ya existe...');
				return;
			}
		}
	}
	public function verificar_stock_receta($id,$diferenciaDeStock)
    {
        $producto = Producto::find($id);

        $verificar_stock = $this->verificarStockRecetaTrait($diferenciaDeStock, $id);

		$permitir_carga_sin_stock = 0;

        if ($verificar_stock[0] == 1) {
            $this->emit('receta_sin_principal', $producto->descripcion); 
            return false;
        } elseif ($verificar_stock[0] == 2) {
            if ($permitir_carga_sin_stock == 1) {
                $this->emit('stock_receta_no_disponible_con_opcion',$verificar_stock[1], $verificar_stock[2]);
            } else $this->emit('stock_receta_no_disponible_sin_opcion',$verificar_stock[1], $verificar_stock[2]);
            return false;
        } elseif ($verificar_stock[0] == 3) {
            $this->emit('receta_sin_detalle', $producto->descripcion);
            return false; 
        } elseif ($verificar_stock[0] == 4) { //stock no disponible CON opción
            $peps = Peps::where('producto_id', $id)->where('comercio_id', $this->comercioId)->sum('resto');
            if ($permitir_carga_sin_stock == 1) {
                $this->emit('stock_no_disponible_con_opcion', $peps, $producto->descripcion, $producto->id);
            } else $this->emit('stock_no_disponible_sin_opcion', $peps, $producto->descripcion);
            return false;
        } elseif ($verificar_stock[0] == 5) { //stock no disponible SIN opción
            $peps = Peps::where('producto_id', $id)->where('comercio_id', $this->comercioId)->sum('resto');
            $this->emit('stock_no_disponible_sin_opcion', $peps, $producto->descripcion);
            return false; 
        // } elseif ($verificar_stock[0] == 4) { //stock no disponible CON opción
        //     $peps = Peps::where('producto_id', $id)->where('comercio_id', $this->comercioId)->sum('resto');
        //     if ($venta_sin_stock_general == 1) {
        //         $this->emit('stock_no_disponible_con_opcion', $peps, $producto->descripcion, $producto->id);
        //     } else $this->emit('stock_no_disponible_sin_opcion', $peps, $producto->descripcion);
        //     return false;
        // } elseif ($verificar_stock[0] == 5) { //stock no disponible SIN opción
        //     $peps = Peps::where('producto_id', $id)->where('comercio_id', $this->comercioId)->sum('resto');
        //     if ($venta_sin_stock_general == 1) {
        //         $this->emit('stock_no_disponible_con_opcion', $peps, $producto->descripcion, $producto->id);
        //     } else $this->emit('stock_no_disponible_sin_opcion', $peps, $producto->descripcion);
        //     //$this->emit('stock_no_disponible_sin_opcion', $peps, $producto->descripcion);
        //     return false; 
        } else return true;
    }
	public function actualizarPreciosCargados()
	{
		//ACTUALIZO LOS IMPORTES QUE FIGUREN EN LOS DETALLE DE FACTURA ABIERTA O PENDIENTE
		//EN DONDE CONTENGAN AL PRODUCTO QUE ESTAMOS MODIFICANDO
		DB::begintransaction();
        try{
			$detalle = Factura::join('detfacturas as df', 'df.factura_id', 'facturas.id')
				->where('facturas.estado', 'abierta')
				->where('facturas.comercio_id', $this->comercioId)
				->where('df.producto_id', $this->selected_id)
				->orWhere('facturas.estado', 'pendiente')
				->where('facturas.comercio_id', $this->comercioId)
				->where('df.producto_id', $this->selected_id)
				->select('facturas.mesa_id', 'df.id', 'df.precio')->get();
			if($detalle->count()){
				foreach ($detalle as $i) {
					$grabar = Detfactura::find($i->id);
					if($i->mesa_id) $grabar->update(['precio' => $this->precio_venta_l1]);
					else $grabar->update(['precio' => $this->precio_venta_l2]);					
				}
			}
			DB::commit();
			session()->flash('msg-ok', 'Producto Actualizado');             
			session()->flash('msg-ok2', 'Facturas actualizadas exitosamente!!!'); 
		}catch (\Exception $e){
			DB::rollback();
			session()->flash('msg-error', '¡¡¡ATENCIÓN!!! Las Facturas no fueron actualizadas...');
		}
		$this->doAction(1);
		return;
	}	
	public function actualizarStockIngredientesProductoVentaCreceta($productoId, $cantidad)
	{
		$ingredientes = DetReceta::join('recetas as r', 'r.id', 'det_recetas.receta_id')
			->join('productos as p', 'p.id', 'r.producto_id')
			->where('p.id', $productoId)->get();
	}		
	public function destroy($id, $comentario)
    {
        if ($id) {
			$record = DetReceta::where('producto_id', $id)->get();
            if(!$record->count()){
				DB::begintransaction();
				try{
					$record = Producto::find($id)->delete();

					//$this->actualizarRecetasRelacionadas($id);

					$audit = Auditoria::create([
						'item_deleted_id' => $id,
						'tabla' => 'Productos',
						'user_delete_id' => auth()->user()->id,
						'comentario' => $comentario,
						'comercio_id' => $this->comercioId
					]);
					DB::commit();  
					$this->emit('registroEliminado');             
				}catch (Exception $e){
					DB::rollback();
					session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se eliminó...');
				}  
			}else $this->emit('eliminarRegistro'); 

            $this->resetInput();
            return;
        }
		
    }
	public function actualizarRecetasRelacionadas($ingredienteId)
    {
        //VERIFICO SI ESTE PRODUCTO FINAL ES, A LA VEZ, INGREDIENTE DE OTRO PRODUCTO ELABORADO
        //SI ES ASÍ, TAMBIÉN DEBO MODIFICAR ESOS PRECIOS  
        $sub_receta = DetReceta::join('recetas as r', 'r.id', 'det_recetas.receta_id')
			->join('productos as p', 'p.id', 'r.producto_id')
			->join('categorias as cat', 'cat.id', 'p.categoria_id')
            ->where('det_recetas.producto_id', $ingredienteId)
            ->where('det_recetas.comercio_id', $this->comercioId)
            ->select('det_recetas.receta_id', 'r.producto_id', 'cat.margen_1', 'cat.margen_2')->get(); 
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
				
				$data_producto_receta = Receta::join('productos as p', 'p.id', 'recetas.producto_id')
					->join('categorias as c', 'c.id', 'p.categoria_id')
					->where('recetas.id', $i->receta_id)                      
					->where('recetas.comercio_id', $this->comercioId)
					->select('p.id', 'c.margen_1', 'c.margen_2')->get();

                //LA VARIABLE '$precio_costo_sub_receta' HACE REFERENCIA AL PRECIO DE COSTO DEL PRODUCTO 
                //QUE CONTIENE AL PRODUCTO CABECERA COMO INGREDIENTE DE SU PROPIA RECETA
                if ($this->calcular_precio_de_venta == 0){
                    //calcula el precio de venta sumando el margen de ganancia al costo del producto
                    $this->precio_venta_sub_receta_sug_l1 = ($precio_costo_sub_receta * $i->margen_1) / 100 + $precio_costo_sub_receta;
                    $this->precio_venta_sub_receta_sug_l2 = ($precio_costo_sub_receta * $i->margen_2) / 100 + $precio_costo_sub_receta;
                    $this->precio_venta_sub_receta_l1 = ($precio_costo_sub_receta * $i->margen_1) / 100 + $precio_costo_sub_receta;
                    $this->precio_venta_sub_receta_l2 = ($precio_costo_sub_receta * $i->margen_2) / 100 + $precio_costo_sub_receta;
                }else{
                    //calcula el precio de venta obteniendo el margen de ganancia sobre el mismo
                    $this->precio_venta_sub_receta_sug_l1 = $precio_costo_sub_receta * 100 / (100 - $i->margen_1);
                    $this->precio_venta_sub_receta_sug_l2 = $precio_costo_sub_receta * 100 / (100 - $i->margen_2);
                    $this->precio_venta_sub_receta_l1 = $precio_costo_sub_receta * 100 / (100 - $i->margen_1);
                    $this->precio_venta_sub_receta_l2 = $precio_costo_sub_receta * 100 / (100 - $i->margen_2);
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
	public function calcular_precio_venta()
	{
		if($this->selected_id == null){
			$this->cambiar_precios = 'cambiar_todo';
		}

		$costo_con_merma = 1;
		if($this->merma > 0) {
			if (strlen($this->merma) == 1) $costo_con_merma = '1.0' . $this->merma;
			else $costo_con_merma = '1.' . $this->merma;
		}
	
		if ($this->tipo <> 1) {
			if($this->cambiar_precios == 'cambiar_todo'){  //modifica todo
				if($this->precio_costo || $this->precio_costo > 0){
					if($this->precio_costo <> '' && $this->categoria <> 'Elegir') {
						$porcentaje = Categoria::where('id', $this->categoria)->select('margen_1', 'margen_2')->get();
						
						if ($this->calcular_precio_de_venta == 0){
							//calcula el precio de venta sumando el margen de ganancia al costo del producto
							$this->precio_venta_sug_l1 = (($this->precio_costo * $porcentaje[0]->margen_1) / 100 + $this->precio_costo) * $costo_con_merma;
							$this->precio_venta_sug_l2 = (($this->precio_costo * $porcentaje[0]->margen_2) / 100 + $this->precio_costo) * $costo_con_merma;
							$this->precio_venta_l1 = (($this->precio_costo * $porcentaje[0]->margen_1) / 100 + $this->precio_costo) * $costo_con_merma;
							$this->precio_venta_l2 = (($this->precio_costo * $porcentaje[0]->margen_2) / 100 + $this->precio_costo) * $costo_con_merma;
						}else{
							//calcula el precio de venta obteniendo el margen de ganancia sobre el mismo
							$this->precio_venta_sug_l1 = ($this->precio_costo * 100 / (100 - $porcentaje[0]->margen_1)) * $costo_con_merma;
							$this->precio_venta_sug_l2 = ($this->precio_costo * 100 / (100 - $porcentaje[0]->margen_2)) * $costo_con_merma;
							$this->precio_venta_l1 = ($this->precio_costo * 100 / (100 - $porcentaje[0]->margen_1)) * $costo_con_merma;
							$this->precio_venta_l2 = ($this->precio_costo * 100 / (100 - $porcentaje[0]->margen_2)) * $costo_con_merma;
						}
						if ($this->redondear_precio_de_venta == 1){
							$this->precio_venta_sug_l1 = round($this->precio_venta_sug_l1);
							$this->precio_venta_sug_l2 = round($this->precio_venta_sug_l2);
							$this->precio_venta_l1 = round($this->precio_venta_l1);
							$this->precio_venta_l2 = round($this->precio_venta_l2);
						}
					}else{
						session()->flash('msg-error', 'Debe elegir una Categoría');
					}				
				}			
			} else {           //modifica solo los precios de venta sugeridos
				if($this->precio_costo <> '' && $this->categoria <> 'Elegir') {
					$porcentaje = Categoria::where('id', $this->categoria)->select('margen_1', 'margen_2')->get();
					
					if ($this->calcular_precio_de_venta == 0){
						//calcula el precio de venta sumando el margen de ganancia al costo del producto
						$this->precio_venta_sug_l1 = (($this->precio_costo * $porcentaje[0]->margen_1) / 100 + $this->precio_costo) * $costo_con_merma;
						$this->precio_venta_sug_l2 = (($this->precio_costo * $porcentaje[0]->margen_2) / 100 + $this->precio_costo) * $costo_con_merma;
					}else{
						//calcula el precio de venta obteniendo el margen de ganancia sobre el mismo
						$this->precio_venta_sug_l1 = ($this->precio_costo * 100 / (100 - $porcentaje[0]->margen_1)) * $costo_con_merma;
						$this->precio_venta_sug_l2 = ($this->precio_costo * 100 / (100 - $porcentaje[0]->margen_2)) * $costo_con_merma;
					}
					if ($this->redondear_precio_de_venta == 1){
						$this->precio_venta_sug_l1 = round($this->precio_venta_sug_l1);
						$this->precio_venta_sug_l2 = round($this->precio_venta_sug_l2);
					}
				}else{
					session()->flash('msg-error', 'Debe elegir una Categoría');
				}
			}
		}

	}	

	public function validarStockNegativo($tipo)
	{
		if ($tipo == "Actual") $this->stock_actual = '';
		if ($tipo == "Ideal") $this->stock_ideal = '';
		if ($tipo == "Mínimo") $this->stock_minimo = '';
	}	
	public function productoHistorialCompras($idProducto)
	{
		$this->idProductoHistorial = $idProducto;
		if($this->idProductoHistorial){
			$this->infoHistorial = Detcompra::join('compras as c', 'c.id', 'det_compras.factura_id')
				->join('proveedores as p', 'p.id', 'c.proveedor_id')
				->join('productos as pr', 'pr.id', 'det_compras.producto_id')
				->where('det_compras.producto_id', $this->idProductoHistorial)
				->where('det_compras.comercio_id', $this->comercioId)
				->select('det_compras.cantidad', 'pr.descripcion',
					'det_compras.precio','c.created_at', 'c.fecha_fact', 'p.nombre_empresa')
				->orderBy('c.fecha_fact', 'desc')->get();
			if($this->infoHistorial->count()) $this->producto = $this->infoHistorial[0]->descripcion;
			else{
				$producto = Producto::find($this->idProductoHistorial);
				$this->producto = $producto->descripcion;
			}
			$this->emit('abrirModalHistorial');
		}else $this->infoHistorial = [];
	}
	///producto proveedor
	public function productoProveedor($idProducto)
	{
		$this->idProducto = $idProducto;
		if($this->idProducto){
			$this->infoProductoProveedor = ProductoProveedor::join('proveedores as p', 'p.id', 'producto_proveedores.proveedor_id')
				->where('producto_proveedores.producto_id', $this->idProducto)
				->where('producto_proveedores.comercio_id', $this->comercioId)
				->select('producto_proveedores.id', 'p.nombre_empresa')->get();
			$descProducto = Producto::where('id', $this->idProducto)->select('descripcion')->first();
			$this->descProducto = $descProducto->descripcion;
			$this->emit('abrirModalProveedor');
		}else $this->infoProductoProveedor = [];

	}
	public function grabarProductoProveedor($idProveedor)
	{
		if($idProveedor == 'Elegir'){
			$this->emit('agregarProveedor');
			return;
		}else{
			DB::begintransaction();
			try{
				$grabar = ProductoProveedor::create([
					'producto_id'  => $this->idProducto,
					'proveedor_id' => $idProveedor,
					'comercio_id'  => $this->comercioId
				]);
				session()->flash('msg-ok', 'Proveedor agregado exitosamente!!');

				DB::commit();               
			}catch (Exception $e){
				DB::rollback();
				session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
			}
			$this->resetInput();
			return;
		}
	}
	public function destroy_productoProveedor($id)
    {
        if ($id) {
            DB::begintransaction();
            try{
                $record = ProductoProveedor::find($id)->delete();
                DB::commit();  
                $this->emit('registroEliminado');             
            }catch (Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se eliminó...');
            }             
			$this->resetInput();
            return;
        }
    } 
}
	
