<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Auditoria;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Subproducto;
use App\Models\TextoBaseComanda;
use App\Models\SectorComanda;
use DB;


class ProductoController extends Component
{
	public $categoria ='Elegir', $tipo = 'Art. Venta', $sector ='0', $texto ='Elegir', $estado='DISPONIBLE';
	public $codigo = null, $codigo_sugerido, $descripcion, $stock, $stock_minimo, $habilitar_model = false;
	public $precio_costo, $precio_venta_l1, $precio_venta_l2;
	public $selected_id = null, $categorias, $sectores, $textos, $producto;
	public $recuperar_registro = 0, $descripcion_soft_deleted, $id_soft_deleted;
	public $action = 1, $search;
	public $salsa = false, $guarnicion = false;
	public $descripcion_sp, $stock_sp, $stock_minimo_sp, $search_sp;
	public $comercioId, $comercioTipo, $modComandas, $modDelivery; 
	
	public function render()
	{
		//busca el comercio que está en sesión
		$this->comercioId = session('idComercio');
		$this->comercioTipo = session('tipoComercio');
		$this->modDelivery = session('modDelivery');
		$this->modComandas = session('modComandas');
		
		$this->categorias = Categoria::select('*')->where('comercio_id', $this->comercioId)->get();
		$this->sectores = SectorComanda::select('*')->where('comercio_id', $this->comercioId)->get();
		$this->textos = TextoBaseComanda::select('*')->where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();
	//	$this->subproductos = Subproducto::select('*')->where('producto_id', $this->selected_id)->where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();

		if($this->selected_id == null) {
			$nuevo_codigo = Producto::select('*')->where('comercio_id', $this->comercioId)->get();
			if ($nuevo_codigo->count() == 0){
				$this->codigo_sugerido = 1;
			}else{
				$nuevo_codigo = Producto::select()
                ->where('comercio_id', $this->comercioId)
				->orderBy('id','desc')->first();
				$this->codigo_sugerido = $nuevo_codigo->codigo + 1;
			}
		}else{
			$this->codigo_sugerido = $this->selected_id;
		}
		if ($this->codigo == null)
		$this->codigo = $this->codigo_sugerido;

		if(strlen($this->search) > 0) {
			$info = Producto::join('categorias as r','r.id','productos.categoria_id')
				->select('productos.*', 'r.descripcion as categoria')
				->where('productos.descripcion', 'like', '%' . $this->search .'%')
				->where('productos.comercio_id', $this->comercioId)
				->orWhere('productos.estado', 'like', '%' . $this->search .'%')
				->where('productos.comercio_id', $this->comercioId)
				->orWhere('r.descripcion', 'like', '%' . $this->search .'%')
				->where('productos.comercio_id', $this->comercioId)
				->orderBy('productos.descripcion', 'asc')
				->get();
		}else {
			$info = Producto::join('categorias as r','r.id','productos.categoria_id')
				->select('productos.*', 'r.descripcion as categoria')
				->where('productos.comercio_id', $this->comercioId)
				->orderBy('productos.descripcion', 'asc')
				->get();
		}
		if(strlen($this->search_sp) > 0) {
			$subproductos = Subproducto::select('*')
				->where('descripcion', 'like', '%' . $this->search_sp .'%')
				->where('producto_id', $this->selected_id)
				->where('comercio_id', $this->comercioId)
				->orderBy('descripcion')->get();
		}else {
			$subproductos = Subproducto::select('*')
				->where('producto_id', $this->selected_id)
				->where('comercio_id', $this->comercioId)
				->orderBy('descripcion')->get();
		}
		return view('livewire.productos.component', [
			'info'         => $info,
			'subproductos' => $subproductos
		]);
	}
	protected $listeners = [
		'deleteRow'             => 'destroy',
		'delete_sp'             => 'destroy_sp',
		'calcular_precio_venta' => 'calcular_precio_venta',
		'validarProducto'       => 'validarProducto',
		'guardar'               => 'StoreOrUpdate', 
		'grabar_texto_base'     => 'grabar_texto_base', 
		'grabar_subproducto'    => 'grabar_subproducto' 
	];
	public function doAction($action)
	{
		$this->action = $action;
		if ($this->action == 1)	$this->resetInput();
	}
	public function resetInput()
	{
		$this->codigo          = null;
		$this->descripcion     = '';
		$this->precio_costo    = null;
		$this->precio_venta_l1 = '';
		$this->precio_venta_l2 = '';
		$this->stock           = null;
		$this->stock_minimo    = null;
		$this->tipo            = 'Art. Venta';
		$this->categoria       = 'Elegir';
		$this->sector          = '0';
		$this->texto           = 'Elegir';
		$this->estado          = 'DISPONIBLE';
		$this->selected_id     = null;
		$this->search          = '';
		$this->search_sp          = '';
		$this->habilitar_model = false;
	}	
	public function edit($id)
	{
		$this->action = 2;
		$record = Producto::find($id);
		$this->selected_id     = $id;
		$this->categoria       = $record->categoria_id;		
		$this->codigo          = $record->codigo;
		$this->descripcion     = $record->descripcion;
		$this->precio_costo    = $record->precio_costo;
		$this->precio_venta_l1 = $record->precio_venta_l1;
		$this->precio_venta_l2 = $record->precio_venta_l2;
		$this->stock           = $record->stock;
		$this->stock_minimo    = $record->stock_minimo;
		$this->tipo            = $record->tipo;
		$this->estado          = $record->estado;
		$this->sector          = $record->sectorcomanda_id;
		$this->texto           = $record->texto_base_comanda_id;
		if($record->guarnicion == 1) $this->guarnicion = true; else $this->guarnicion = false;
		if($record->salsa == 1) $this->salsa = true; else $this->salsa = false;
	}
	public function agregar_sp()
	{
		$this->action = 3;
	}	
	public function edit_sp()
	{
		$this->action = 3;
		$record = Subproducto::find($id);
		$this->selected_id     = $id;
		$this->categoria       = $record->categoria_id;		
		$this->codigo          = $record->codigo;
		$this->descripcion     = $record->descripcion;
		$this->precio_costo    = $record->precio_costo;
		$this->precio_venta_l1 = $record->precio_venta_l1;
		$this->precio_venta_l2 = $record->precio_venta_l2;
		$this->stock           = $record->stock;
		$this->stock_minimo    = $record->stock_minimo;
		$this->tipo            = $record->tipo;
		$this->estado          = $record->estado;
		$this->sector          = $record->sectorcomanda_id;
		$this->texto           = $record->texto_base_comanda_id;
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
	public function grabar_subproducto($id_subproducto, $texto, $stock_sp, $stock_minimo_sp)
	{
		$texto_sp = ucwords($texto);
		if($stock_sp) $stock = $stock_sp; else $stock = null;
		if($stock_minimo_sp) $stock_minimo = $stock_minimo_sp; else $stock_minimo = null;
		
        $this->validate([
            'texto' => 'required'
        ]);
        DB::begintransaction();
        try{
			if($id_subproducto){
				$existeSp = Subproducto::where('descripcion', $texto)
				->where('id', '<>', $id_subproducto)
				->where('comercio_id', $this->comercioId)				
				->withTrashed()->get();
				if($existeSp->count() && $existeSp[0]->deleted_at != null) {
					session()->flash('info', 'El Subproducto que desea modificar ya existe pero fué eliminado anteriormente, para recuperarlo haga click en el botón "Recuperar registro"');
					$this->action = 1;
					$this->recuperar_registro = 2;
					$this->descripcion_soft_deleted = $existeSp[0]->descripcion;
					$this->id_soft_deleted = $existeSp[0]->id;
					return;				
				}elseif($existeSp->count()) {
					session()->flash('info', 'El Producto ya existe...');
					return;
				}
			}else {
				$existeSp = Subproducto::where('descripcion', $texto)
				->where('comercio_id', $this->comercioId)->withTrashed()->get();
				
				if($existeSp->count() > 0 && $existeSp[0]->deleted_at != null) {
					session()->flash('info', 'El Subproducto que desea agregar fué eliminado anteriormente, para recuperarlo haga click en el botón "Recuperar registro"');
					$this->action = 1;
					$this->recuperar_registro = 2;
					$this->descripcion_soft_deleted = $existeSp[0]->descripcion;
					$this->id_soft_deleted = $existeSp[0]->id;
					return;
				}elseif($existeSp->count()) {
					session()->flash('info', 'El Subproducto ya existe...');
					$this->resetInput();
					return;
				}
			}
			if($id_subproducto){ //modifica
				$existe = Subproducto::find($id_subproducto);
				$existe->update([
					'descripcion'  => ucwords($texto_sp), 
					'stock'        => $stock, 
					'stock_minimo' => $stock_minimo,
				]);
			}else{       //crea
				$texto = Subproducto::create([
					'producto_id'  => $this->selected_id,
					'descripcion'  => ucwords($texto_sp), 
					'stock'        => $stock, 
					'stock_minimo' => $stock_minimo, 
					'comercio_id'  => $this->comercioId            
				]);	
			}
			if($id_subproducto) $this->emit('subproducto_modificado');
			else $this->emit('subproducto_creado');
            DB::commit(); 
        }catch (\Exception $e){
            DB::rollback();
			$this->emit('registro_no_grabado');
        }
		$this->doAction(3);
		return;
	}
	public function StoreOrUpdate($salsa, $guarnicion)
	{
		if($salsa) $salsa = '1'; else $salsa = '0';
        if($guarnicion) $guarnicion = '1'; else $guarnicion = '0';
		if($this->modComandas != "1"){
			$this->sector = null;
			$this->texto = null;
		}
		$this->validate([
			'categoria' => 'not_in:Elegir',
		]);
		
		$this->validate([
			'descripcion'     => 'required',
			'estado'          => 'required',
			'tipo'            => 'required',
			'precio_venta_l1' => 'required'
		]);
			
		DB::begintransaction();
        try{
			if($this->selected_id > 0) {
				$existeProducto = Producto::where('descripcion', $this->descripcion)
				->where('id', '<>', $this->selected_id)
				->where('comercio_id', $this->comercioId)				
				->withTrashed()->get();
                if($existeProducto->count() && $existeProducto[0]->deleted_at != null) {
					session()->flash('info', 'El Producto que desea modificar ya existe pero fué eliminado anteriormente, para recuperarlo haga click en el botón "Recuperar registro"');
                    $this->action = 1;
                    $this->recuperar_registro = 1;
                    $this->descripcion_soft_deleted = $existeProducto[0]->descripcion;
                    $this->id_soft_deleted = $existeProducto[0]->id;
                    return;				
				}elseif($existeProducto->count()) {
					session()->flash('info', 'El Producto ya existe...');
					$this->resetInput();
					return;
				}
				
				$existeCodigo = Producto::where('codigo', $this->codigo)
					->where('id', '<>', $this->selected_id)
					->where('comercio_id', $this->comercioId)
					->withTrashed()->get();
                if($existeCodigo->count() > 0 && $existeCodigo[0]->deleted_at != null) {
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
			}else {
				$existeProducto = Producto::where('descripcion', $this->descripcion)
				->where('comercio_id', $this->comercioId)->withTrashed()->get();
				
				if($existeProducto->count() > 0 && $existeProducto[0]->deleted_at != null) {
					session()->flash('info', 'El Producto que desea agregar fué eliminado anteriormente, para recuperarlo haga click en el botón "Recuperar registro"');
					$this->action = 1;
					$this->recuperar_registro = 1;
					$this->descripcion_soft_deleted = $existeProducto[0]->descripcion;
					$this->id_soft_deleted = $existeProducto[0]->id;
					return;
				}elseif($existeProducto->count()) {
					session()->flash('info', 'El Producto ya existe...');
					$this->resetInput();
					return;
				}
				
				$existeCodigo = Producto::where('codigo', $this->codigo)
				->where('comercio_id', $this->comercioId)		
				->withTrashed()->get();
                if($existeCodigo->count() > 0 && $existeCodigo[0]->deleted_at != null) {
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
			if($this->selected_id <=0) {
				$producto = Producto::create([
					'codigo'                => $this->codigo_sugerido,
					'descripcion'           => ucwords($this->descripcion),
					'precio_costo'          => $this->precio_costo,
					'precio_venta_l1'       => $this->precio_venta_l1,
					'precio_venta_l2'       => $this->precio_venta_l2,
					'stock'                 => $this->stock,
					'stock_minimo'          => $this->stock_minimo,
					'tipo'                  => $this->tipo,
					'categoria_id'          => $this->categoria,
					'estado'                => $this->estado,
					'comercio_id'           => $this->comercioId,
					'salsa'                 => $salsa,
					'guarnicion'            => $guarnicion,
					'sectorcomanda_id'      => $this->sector,
					'texto_base_comanda_id' => $this->texto
				]);
			}else {				
				$record = Producto::find($this->selected_id);
				$record->update([
					'descripcion'  	        => ucwords($this->descripcion),
					'precio_costo' 	        => $this->precio_costo,			
					'precio_venta_l1'       => $this->precio_venta_l1,
					'precio_venta_l2'       => $this->precio_venta_l2,			
					'stock'                 => $this->stock,
					'stock_minimo'          => $this->stock_minimo,
					'tipo'         	        => $this->tipo,
					'categoria_id' 	        => $this->categoria,
					'estado'       	        => $this->estado,
					'salsa'                 => $salsa,
					'guarnicion'            => $guarnicion,
					'sectorcomanda_id'      => $this->sector,
					'texto_base_comanda_id' => $this->texto
				]);
				$this->action = 1;
			}
			if($this->selected_id > 0) session()->flash('msg-ok', 'Producto Actualizado');       
			else session()->flash('msg-ok', 'Producto Creado');

			DB::commit();               
		}catch (Exception $e){
			DB::rollback();
			session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
		}
		$this->resetInput();
		return;
	}			
	public function destroy($id, $comentario)
    {
        if ($id) {
            DB::begintransaction();
            try{
                $record = Producto::find($id)->delete();
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
            $this->resetInput();
            return;
        }
    } 			
	public function destroy_sp($id, $comentario)
    {
        if ($id) {
            DB::begintransaction();
            try{
                $record = Subproducto::find($id)->delete();
                $audit = Auditoria::create([
                    'item_deleted_id' => $id,
                    'tabla'           => 'Subproductos',
                    'user_delete_id'  => auth()->user()->id,
                    'comentario'      => $comentario,
                    'comercio_id'     => $this->comercioId
                ]);
                DB::commit();  
                $this->emit('registroEliminado');             
            }catch (Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se eliminó...');
            }             
            $this->doAction(3);
            return;
        }
    } 			
	public function calcular_precio_venta()
	{
		if($this->precio_costo <> '' && $this->categoria <> 'Elegir') {
			$porcentaje = Categoria::where('id', $this->categoria)->select('margen_1', 'margen_2')->get();
			$this->precio_venta_l1 = number_format($this->precio_costo * 100 / (100 - $porcentaje[0]->margen_1),2);
			$this->precio_venta_l2 = number_format($this->precio_costo * 100 / (100 - $porcentaje[0]->margen_2),2);
		}else {
			session()->flash('msg-error', 'Debe elegir una Categoría');
		}
	}	
	public function validarProducto()
	{
		if($this->selected_id > 0) {
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
			}else $this->habilitar_model = true; //habilito la presentación del model para agregar Texto Base
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
				session()->flash('info', 'El Producto ya existe...');
				$this->resetInput();
				return;
			}else $this->habilitar_model = true; //habilito la presentación del model para agregar Texto Base
		}
	}
}
	
