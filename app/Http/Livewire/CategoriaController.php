<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Auditoria;
use App\Models\Categoria;
use App\Models\Comercio;
use App\Models\Producto;
use App\Models\Rubro;
use App\Models\TipoArticulo;
use DB;

class CategoriaController extends Component
{
    public $comercioId, $modComandas, $selected_id, $search, $action, $calcular_precio_de_venta;  
    public $recuperar_registro, $descripcion_soft_deleted, $id_soft_deleted;
    public $descripcion, $margen_1, $margen_2, $rubro, $tipo;
	public $tipos, $rubros;
    public $mostrar_al_vender;

    public function mount()
    {
        $this->comercioId = session('idComercio');
        $this->modComandas = session('modComandas');

        $record = Comercio::find($this->comercioId);
        $this->calcular_precio_de_venta = $record->calcular_precio_de_venta;

        $this->action = 1;
        $this->recuperar_registro = 0;
        $this->tipo = 'Elegir';
        $this->rubro ='Elegir';
        $this->tipos = TipoArticulo::all();
        $this->rubros = Rubro::all();
    }

    public function render()
    {                
        if(strlen($this->search) > 0)
        {
            $info = Categoria::join('rubros as r', 'r.id', 'categorias.rubro_id')
                ->join('tipo_articulos as t', 't.id', 'categorias.tipo_id')
                ->where('categorias.descripcion', 'like', '%' .  $this->search . '%')
                ->where('categorias.comercio_id', $this->comercioId)
                ->orWhere('r.descripcion', 'like', '%' .  $this->search . '%')
                ->where('categorias.comercio_id', $this->comercioId)
                ->orWhere('t.descripcion', 'like', '%' .  $this->search . '%')
                ->where('categorias.comercio_id', $this->comercioId)
                ->select('categorias.*', 'r.descripcion as rubro_descripcion',
                    't.descripcion as tipo_descripcion') 
                ->orderby('categorias.descripcion','desc')->get();
        }else {
            $info = Categoria::join('rubros as r', 'r.id', 'categorias.rubro_id')
                ->join('tipo_articulos as t', 't.id', 'categorias.tipo_id')
                ->where('categorias.comercio_id', $this->comercioId)
                ->select('categorias.*', 'r.descripcion as rubro_descripcion',
                    't.descripcion as tipo_descripcion')
                ->orderBy('categorias.descripcion', 'asc')->get();
        }

        return view('livewire.categorias.component', [
            'info'  => $info
        ]);
    }    
    protected $listeners = [
        'StoreOrUpdate',       
        'deleteRow'
    ];
    public function doAction($action)
    {
        $this->action = $action;
        $this->resetInput();
    }
    private function resetInput()
    {
        $this->descripcion       = '';
        $this->margen_1          = '';
        $this->margen_2          = '';
        $this->tipo              = 'Elegir';
        $this->rubro             = 'Elegir';
        $this->selected_id       = null;    
        $this->search            = '';
    }
    public function edit($id)
    {
        $this->action = 2;
        $categoria = Categoria::findOrFail($id);
        $this->selected_id = $id;
        $this->descripcion = $categoria->descripcion;
        $this->margen_1    = $categoria->margen_1;
        $this->margen_2    = $categoria->margen_2;
        $this->tipo        = $categoria->tipo_id;
        $this->rubro       = $categoria->rubro_id;
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
            Categoria::onlyTrashed()->find($id)->restore();
            $audit = Auditoria::create([
                'item_deleted_id' => $id,
                'tabla'           => 'Categorías',
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
        $this->validaciones();
        
        if (!$this->verificarSiExiste()) {
            
            DB::begintransaction();
            try{
                if($this->selected_id > 0) {
                    $record = Categoria::find($this->selected_id);
                    $record->update([
                        'descripcion' => mb_strtoupper($this->descripcion), 
                        'margen_1'    => $this->margen_1,
                        'margen_2'    => $this->margen_2,        
                        'tipo_id'     => $this->tipo,
                        'rubro_id'    => $this->rubro
                    ]);
                    $this->action = 1;                    
                }else {   
                    $category =  Categoria::create([
                        'descripcion' => mb_strtoupper($this->descripcion), 
                        'margen_1'    => $this->margen_1,
                        'margen_2'    => $this->margen_2,  
                        'tipo_id'     => $this->tipo,    
                        'rubro_id'    => $this->rubro,
                        'comercio_id' => $this->comercioId            
                    ]);                 
                }
                if($this->selected_id) session()->flash('msg-ok', 'Categoria Actualizada');            
                else session()->flash('msg-ok', 'Categoria Creada');            
    
                DB::commit(); 
            }catch (\Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
            }
            $this->resetInput();
            return;
        }
    } 
    
    public function validaciones()
    {
        $this->validate([
            'descripcion' => 'required',
            'rubro'       => 'not_in:Elegir',
            'tipo'        => 'not_in:Elegir'
        ]);

        // para el caso de que calcula el precio de venta obteniendo el margen de ganancia sobre el mismo
        if ($this->calcular_precio_de_venta == 1 && $this->tipo == 2 ||
            $this->calcular_precio_de_venta == 1 && $this->tipo == 3){   
                if ($this->modComandas == "1") {
                    $this->validate([
                        'margen_1' => 'required|integer|between:1,99',                 
                        'margen_2' => 'required|integer|between:1,99'
                    ],
                    [
                        'margen_1.required' => 'El Margen Lista Salón es obligatorio.',
                        'margen_2.required' => 'El Margen Lista Delivery es obligatorio.',
                        'margen_1.integer' => 'El Margen Lista Salón debe ser un entero.',
                        'margen_2.integer' => 'El Margen Lista Delivery debe ser un entero.',
                        'margen_1.between' => 'El Margen Lista Salón debe ser entre 1 y 99.',
                        'margen_2.between' => 'El Margen Lista Delivery debe ser entre 1 y 99.'
                    ]);
                } else {
                    $this->validate([
                        'margen_1' => 'required|integer|between:1,99',                 
                        'margen_2' => 'required|integer|between:1,99'
                    ],
                    [
                        'margen_1.required' => 'El Margen Lista 1 es obligatorio.',
                        'margen_2.required' => 'El Margen Lista 2 es obligatorio.',
                        'margen_1.integer' => 'El Margen Lista 1 debe ser un entero.',
                        'margen_2.integer' => 'El Margen Lista 2 debe ser un entero.',
                        'margen_1.between' => 'El Margen Lista 1 debe ser entre 1 y 99.',
                        'margen_2.between' => 'El Margen Lista 2 debe ser entre 1 y 99.'
                    ]);
                } 
        } elseif ($this->calcular_precio_de_venta == 0 && $this->tipo == 2 ||
                  $this->calcular_precio_de_venta == 0 && $this->tipo == 3) {
            if ($this->modComandas == "1") {
                $this->validate([
                    'margen_1'    => 'required|integer|min:1',
                    'margen_2'    => 'required|integer|min:1'
                ],
                [
                    'margen_1.required' => 'El Margen Lista Salón es obligatorio.',
                    'margen_2.required' => 'El Margen Lista Delivery es obligatorio.',
                    'margen_1.integer' => 'El Margen Lista Salón debe ser un entero.',
                    'margen_2.integer' => 'El Margen Lista Delivery debe ser un entero.',
                    'margen_1.min:1' => 'El Margen Lista Salón debe ser mayor a 1.',
                    'margen_2.min:1' => 'El Margen Lista Delivery debe ser mayor a 1.'
                ]);
            } else {
                $this->validate([
                    'margen_1'    => 'required|integer|min:1',
                    'margen_2'    => 'required|integer|min:1'
                ],
                [
                    'margen_1.required' => 'El Margen Lista 1 es obligatorio.',
                    'margen_2.required' => 'El Margen Lista 2 es obligatorio.',
                    'margen_1.integer' => 'El Margen Lista 1 debe ser un entero.',
                    'margen_2.integer' => 'El Margen Lista 2 debe ser un entero.',
                    'margen_1.min:1' => 'El Margen Lista 1 debe ser mayor a 1.',
                    'margen_2.min:1' => 'El Margen Lista 2 debe ser mayor a 1.'
                ]);
            }            
        }
    }

    public function verificarSiExiste()
    {
        if($this->selected_id > 0) {
            $existe = Categoria::where('descripcion', $this->descripcion)
                ->where('tipo_id', $this->tipo)
                ->where('rubro_id', $this->rubro)
                ->where('id', '<>', $this->selected_id)
                ->where('comercio_id', $this->comercioId)
                ->withTrashed()->get();
            if($existe->count() && $existe[0]->deleted_at != null) {
                $this->action = 1;
                $this->recuperar_registro = 1;
                $this->descripcion_soft_deleted = $existe[0]->descripcion;
                $this->id_soft_deleted = $existe[0]->id;
                return true;
            }elseif($existe->count()) {
                session()->flash('info', 'La Categoría ya existe...');
                return true;
            }
        }else {
            $existe = Categoria::where('descripcion', $this->descripcion)
            ->where('tipo_id', $this->tipo)
            ->where('rubro_id', $this->rubro)
            ->where('comercio_id', $this->comercioId)->withTrashed()->get();

            if($existe->count() && $existe[0]->deleted_at != null) {
                $this->action = 1;
                $this->recuperar_registro = 1;
                $this->descripcion_soft_deleted = $existe[0]->descripcion;
                $this->id_soft_deleted = $existe[0]->id;
                return true;
            }elseif($existe->count()) {
                session()->flash('info', 'La Categoría ya existe...');
                return true;
            }
        }
    }

    public function deleteRow($id, $comentario)
    {
        if ($id) {
            $record = Producto::where('categoria_id', $id)->get();
            if(!$record->count()){
                DB::begintransaction();
                try{
                    $categoria = Categoria::find($id)->delete();
                    $audit = Auditoria::create([
                        'item_deleted_id' => $id,
                        'tabla' => 'Categorías',
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
}