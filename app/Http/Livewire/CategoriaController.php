<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Auditoria;
use App\Models\Categoria;
use DB;

class CategoriaController extends Component
{
	public  $descripcion, $margen, $descripcion_soft_deleted, $id_soft_deleted;
    public  $selected_id, $search, $comentario = '';  
    public $comercioId, $action = 1, $recuperar_registro = 0;

    public function render()
    {
         //busca el comercio que está en sesión
         $this->comercioId = session('idComercio');

        if(strlen($this->search) > 0)
        {
            $info = Categoria::where('descripcion', 'like', '%' .  $this->search . '%')
                    ->where('comercio_id', $this->comercioId) 
                    ->orderby('descripcion','desc')->get();
            return view('livewire.categorias.component', [
                'info' =>$info
            ]);
        }
        else {
           return view('livewire.categorias.component', [
            'info' => Categoria::orderBy('descripcion', 'asc')
                        ->where('comercio_id', $this->comercioId)->get()
        ]);
       }
    }

    public function doAction($action)
    {
        $this->action = $action;
        $this->resetInput();
    }

    private function resetInput()
    {
        $this->descripcion = '';
        $this->margen = '';
        $this->selected_id = null;    
        $this->search = '';
        $this->comentario = '';
    }

    public function edit($id)
    {
        $this->action = 2;
        $record = Categoria::findOrFail($id);
        $this->selected_id = $id;
        $this->descripcion = $record->descripcion;
        $this->margen = $record->margen;
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
            'descripcion' => 'required'
        ]);
        DB::begintransaction();
        try{
            if($this->selected_id > 0) {
                $existe = Categoria::where('descripcion', $this->descripcion)
                ->where('id', '<>', $this->selected_id)
                ->where('comercio_id', $this->comercioId)
                ->withTrashed()->get();

                if($existe->count() > 0 && $existe[0]->deleted_at != null) {
                    session()->flash('info', 'La Categoría que desea modificar ya existe pero fué eliminada anteriormente, para recuperarla haga click en el botón "Recuperar registro"');
                    $this->action = 1;
                    $this->recuperar_registro = 1;
                    $this->descripcion_soft_deleted = $existe[0]->descripcion;
                    $this->id_soft_deleted = $existe[0]->id;
                    return;
                }elseif( $existe->count() > 0) {
                    session()->flash('info', 'La Categoría ya existe...');
                    $this->resetInput();
                    return;
                }
            }else {
                $existe = Categoria::where('descripcion', $this->descripcion)
                ->where('comercio_id', $this->comercioId)->withTrashed()->get();

                if($existe->count() > 0 && $existe[0]->deleted_at != null) {
                    session()->flash('info', 'La Categoría que desea agregar ya existe pero fué eliminado anteriormente, para recuperarlo haga click en el botón "Recuperar registro"');
                    $this->action = 1;
                    $this->recuperar_registro = 1;
                    $this->descripcion_soft_deleted = $existe[0]->descripcion;
                    $this->id_soft_deleted = $existe[0]->id;
                    return;
                }elseif($existe->count() > 0 ) {
                    session()->flash('info', 'La Categoría ya existe...');
                    $this->resetInput();
                    return;
                }
            }
            if($this->selected_id <= 0) {
                $category =  Categoria::create([
                    'descripcion' => strtoupper($this->descripcion),            
                    'margen' => $this->margen,
                    'comercio_id' => $this->comercioId            
                ]);
            }else {   
                $record = Categoria::find($this->selected_id);
                $record->update([
                    'descripcion' => strtoupper($this->descripcion),
                    'margen' => $this->margen
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
    
    protected $listeners = [
        'deleteRow'=>'destroy'        
    ];  
       
    public function destroy($id)
    {
        if ($id) {
            DB::begintransaction();
            try{
                $categoria = Categoria::find($id)->delete();
                $audit = Auditoria::create([
                    'item_deleted_id' => $id,
                    'tabla' => 'Categorias',
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