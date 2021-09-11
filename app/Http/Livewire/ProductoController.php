<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Auditoria;
use App\Models\Categoria;
use App\Models\Producto;
use DB;


class ProductoController extends Component
{
	public $categoria ='Elegir', $tipo = 'Art. Venta', $estado='DISPONIBLE';
	public $codigo = null, $codigo_sugerido, $descripcion, $stock, $stock_minimo, $habilitar_model = false;
	public $pc_salon, $pv_salon, $precio_costo, $precio_venta_l1, $precio_venta_l2;
	public $selected_id = null, $categorias, $sectores, $textos;
	public $comercioId, $comercioTipo , $action = 1, $search;
	public $recuperar_registro = 0, $descripcion_soft_deleted, $id_soft_deleted;
	
	public function render()
	{
		//busca el comercio que está en sesión
		$this->comercioId = session('idComercio');
		$this->comercioTipo = session('tipoComercio');
		
		$this->categorias = Categoria::select('*')->where('comercio_id', $this->comercioId)->get();

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
			$info = Producto::leftjoin('categorias as r','r.id','productos.categoria_id')
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
			$info = Producto::leftjoin('categorias as r','r.id','productos.categoria_id')
				->select('productos.*', 'r.descripcion as categoria')
				->where('productos.comercio_id', $this->comercioId)
				->orderBy('productos.descripcion', 'asc')
				->get();
		}
		return view('livewire.productos.component', [
			'info' => $info
		]);
	}
	protected $listeners = [
		'deleteRow'             => 'destroy',
		'calcular_precio_venta' => 'calcular_precio_venta',
		'validarProducto'       => 'validarProducto',
		'guardar'               => 'StoreOrUpdate'
	];
	public function doAction($action)
	{
		$this->action = $action;
		$this->resetInput();
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
	}	
	public function volver()
    {
		$this->recuperar_registro = 0;
        $this->resetInput();
        return; 
    }	
    public function RecuperarRegistro($id)
    {
		DB::begintransaction();
        try{
			Producto::onlyTrashed()->find($id)->restore();
			$audit = Auditoria::create([
                'item_deleted_id' => $id,
                'tabla'           => 'Productos',
                'estado'          => '1',
                'user_delete_id'  => auth()->user()->id,
                'comentario'      => '',
                'comercio_id'     => $this->comercioId
            ]);
            session()->flash('msg-ok', 'Registro recuperado');
            $this->volver();
            
            DB::commit();               
        }catch (\Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se recuperó...');
        }
    }
	public function StoreOrUpdate()
	{
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
					'codigo'          => $this->codigo_sugerido,
					'descripcion'     => ucwords($this->descripcion),
					'precio_costo'    => $this->precio_costo,
					'precio_venta_l1' => $this->precio_venta_l1,
					'precio_venta_l2' => $this->precio_venta_l2,
					'stock'           => $this->stock,
					'stock_minimo'    => $this->stock_minimo,
					'tipo'            => $this->tipo,
					'categoria_id'    => $this->categoria,
					'estado'          => $this->estado,
					'comercio_id'     => $this->comercioId
				]);
			}else {				
				$record = Producto::find($this->selected_id);
				$record->update([
					'descripcion'  	  => ucwords($this->descripcion),
					'precio_costo' 	  => $this->precio_costo,			
					'precio_venta_l1' => $this->precio_venta_l1,
					'precio_venta_l2' => $this->precio_venta_l2,			
					'stock'           => $this->stock,
					'stock_minimo'    => $this->stock_minimo,
					'tipo'         	  => $this->tipo,
					'categoria_id' 	  => $this->categoria,
					'estado'       	  => $this->estado
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
	public function calcular_precio_venta()
	{
		if($this->precio_costo <> '' && $this->categoria <> 'Elegir') {
			$porcentaje = Categoria::where('id', $this->categoria)->select('margen_1', 'margen_2')->get();
			$this->precio_venta_l1 = $this->precio_costo + ($this->precio_costo * $porcentaje[0]->margen_1 / 100);
			$this->precio_venta_l2 = $this->precio_costo + ($this->precio_costo * $porcentaje[0]->margen_2 / 100);
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
	
