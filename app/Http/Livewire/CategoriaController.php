<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Auditoria;
use App\Models\Categoria;
use App\Models\Producto;
use DB;

class CategoriaController extends Component
{
	public $descripcion, $margen_1, $margen_2, $descripcion_soft_deleted, $id_soft_deleted;
    public $selected_id, $search, $action = 1, $recuperar_registro = 0;  
    public $comercioId, $comercioTipo, $modComandas, $modDelivery;

    public function render()
    {
         //busca el comercio que está en sesión
         $this->comercioId = session('idComercio');
         
         $this->comercioTipo = session('tipoComercio');
         $this->modComandas = session('modComandas');
         $this->modDelivery = session('modDelivery');

        if(strlen($this->search) > 0)
        {
            $info = Categoria::where('descripcion', 'like', '%' .  $this->search . '%')
                        ->where('comercio_id', $this->comercioId) 
                        ->orderby('descripcion','desc')->get();
        }else {
            $info = Categoria::orderBy('descripcion', 'asc')
                        ->where('comercio_id', $this->comercioId)->get();
        }
        return view('livewire.categorias.component', [
            'info' => $info
        ]);
    }    
    protected $listeners = [
        'deleteRow'=>'destroy'        
    ];
    public function doAction($action)
    {
        $this->action = $action;
        $this->resetInput();
    }
    private function resetInput()
    {
        $this->descripcion = '';
        $this->margen_1 = '';
        $this->margen_2 = '';
        $this->selected_id = null;    
        $this->search = '';
    }
    public function edit($id)
    {
        $this->action = 2;
        $record = Categoria::findOrFail($id);
        $this->selected_id = $id;
        $this->descripcion = $record->descripcion;
        $this->margen_1 = $record->margen_1;
        $this->margen_2 = $record->margen_2;
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
        $this->validate([
            'descripcion' => 'required',
            'margen_1'    => 'required|integer|min:1|max: 99',
            'margen_2'    => 'integer|min:1|max: 99'
        ]);
        DB::begintransaction();
        try{
            if($this->selected_id > 0) {
                $existe = Categoria::where('descripcion', $this->descripcion)
                ->where('id', '<>', $this->selected_id)
                ->where('comercio_id', $this->comercioId)
                ->withTrashed()->get();
                if($existe->count() && $existe[0]->deleted_at != null) {
                    //session()->flash('info', 'La Categoría que desea modificar ya existe pero fué eliminada anteriormente, para recuperarla haga click en el botón "Recuperar registro"');
                    $this->action = 1;
                    $this->recuperar_registro = 1;
                    $this->descripcion_soft_deleted = $existe[0]->descripcion;
                    $this->id_soft_deleted = $existe[0]->id;
                    return;
                }elseif($existe->count()) {
                    session()->flash('info', 'La Categoría ya existe...');
                    $this->resetInput();
                    return;
                }
            }else {
                $existe = Categoria::where('descripcion', $this->descripcion)
                ->where('comercio_id', $this->comercioId)->withTrashed()->get();

                if($existe->count() && $existe[0]->deleted_at != null) {
                    //session()->flash('info', 'La Categoría que desea agregar ya existe pero fué eliminado anteriormente, para recuperarlo haga click en el botón "Recuperar registro"');
                    $this->action = 1;
                    $this->recuperar_registro = 1;
                    $this->descripcion_soft_deleted = $existe[0]->descripcion;
                    $this->id_soft_deleted = $existe[0]->id;
                    return;
                }elseif($existe->count()) {
                    session()->flash('info', 'La Categoría ya existe...');
                    $this->resetInput();
                    return;
                }
            }
            if($this->selected_id <= 0) {
                $category =  Categoria::create([
                    'descripcion' => strtoupper($this->descripcion),            
                    'margen_1'    => $this->margen_1,
                    'margen_2'    => $this->margen_2,
                    'comercio_id' => $this->comercioId            
                ]);
            }else {   
                $record = Categoria::find($this->selected_id);
                $record->update([
                    'descripcion' => strtoupper($this->descripcion),
                    'margen_1'    => $this->margen_1,
                    'margen_2'    => $this->margen_2
                ]);
                $this->action = 1;              
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
    public function destroy($id, $comentario)
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