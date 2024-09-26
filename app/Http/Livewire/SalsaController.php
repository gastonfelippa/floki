<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Auditoria;
use App\Models\Salsa;
use DB;

class SalsaController extends Component
{
	public $descripcion, $descripcion_soft_deleted, $id_soft_deleted;
    public $selected_id, $search;  
    public $comercioId, $action = 1, $recuperar_registro = 0;

    public function render()
    {
         //busca el comercio que está en sesión
         $this->comercioId = session('idComercio');
         session(['facturaPendiente' => null]);  

        if(strlen($this->search) > 0)
        {
            $info = Salsa::where('descripcion', 'like', '%' .  $this->search . '%')
                    ->where('comercio_id', $this->comercioId) 
                    ->orderby('descripcion','desc')->get();
            return view('livewire.salsas.component', [
                'info' =>$info
            ]);
        }
        else {
           return view('livewire.salsas.component', [
            'info' => Salsa::orderBy('descripcion', 'asc')
                        ->where('comercio_id', $this->comercioId)->get()
        ]);
       }
    }    
    protected $listeners = [
        'deleteRow'=>'destroy',
        'ver_receta'        
    ];
    public function ver_receta($id)
	{
        session(['producto_receta_id' => null]);
		session(['salsa_receta_id' => $id]); 
		return redirect()->to('/recetas');

	}
    public function doAction($action)
    {
        $this->action = $action;
        $this->resetInput();
    }
    private function resetInput()
    {
        $this->descripcion = '';
        $this->selected_id = null;    
        $this->search = '';
    }
    public function edit($id)
    {
        $this->action = 2;
        $record = Salsa::findOrFail($id);
        $this->selected_id = $id;
        $this->descripcion = $record->descripcion;
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
            Salsa::onlyTrashed()->find($id)->restore();
            $audit = Auditoria::create([
                'item_deleted_id' => $id,
                'tabla'           => 'Salsas',
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
            'descripcion' => 'required'
        ]);
        DB::begintransaction();
        try{
            if($this->selected_id > 0) {
                $existe = Salsa::where('descripcion', $this->descripcion)
                ->where('id', '<>', $this->selected_id)
                ->where('comercio_id', $this->comercioId)
                ->withTrashed()->get();
                if($existe->count() && $existe[0]->deleted_at != null) {
                    $this->action = 1;
                    $this->recuperar_registro = 1;
                    $this->descripcion_soft_deleted = $existe[0]->descripcion;
                    $this->id_soft_deleted = $existe[0]->id;
                    return;
                }elseif($existe->count()) {
                    session()->flash('info', 'La Salsa ya existe...');
                    $this->resetInput();
                    return;
                }
            }else {
                $existe = Salsa::where('descripcion', $this->descripcion)
                ->where('comercio_id', $this->comercioId)->withTrashed()->get();

                if($existe->count() && $existe[0]->deleted_at != null) {
                    $this->action = 1;
                    $this->recuperar_registro = 1;
                    $this->descripcion_soft_deleted = $existe[0]->descripcion;
                    $this->id_soft_deleted = $existe[0]->id;
                    return;
                }elseif($existe->count()) {
                    session()->flash('info', 'La Salsa ya existe...');
                    $this->resetInput();
                    return;
                }
            }
            if($this->selected_id <= 0) {
                $category =  Salsa::create([
                    'descripcion' => ucwords($this->descripcion), 
                    'comercio_id' => $this->comercioId            
                ]);
            }else {   
                $record = Salsa::find($this->selected_id);
                $record->update([
                    'descripcion' => ucwords($this->descripcion)
                ]);
                $this->action = 1;              
            }
            if($this->selected_id) session()->flash('msg-ok', 'Salsa Actualizada');            
            else session()->flash('msg-ok', 'Salsa Creada');            
 
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
            DB::begintransaction();
            try{
                $salsa = Salsa::find($id)->delete();
                $audit = Auditoria::create([
                    'item_deleted_id' => $id,
                    'tabla' => 'Salsas',
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
}
