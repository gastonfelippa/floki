<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Auditoria;
use App\Models\OtroDebito;
use DB;

class OtroDebitoController extends Component
{
	public $descripcion, $importe;
    public $otrodebito;
    public $comercioId, $action = 1, $selected_id, $search;  
    public $descripcion_soft_deleted, $id_soft_deleted, $recuperar_registro = 0;

    public function render()
    {
         //busca el comercio que está en sesión
         $this->comercioId = session('idComercio');

        if(strlen($this->search) > 0) {
            $info = OtroDebito::where('descripcion', 'like', '%' .  $this->search . '%')
                    ->where('comercio_id', $this->comercioId) 
                    ->orderby('descripcion','desc')->get();
        }else {
            $info = OtroDebito::orderBy('descripcion', 'asc')
                    ->where('comercio_id', $this->comercioId)->get();
        }
        return view('livewire.otrosdebitos.component', ['info' => $info]);
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
        $this->importe = '';
        $this->selected_id = null;    
        $this->search = '';
    }
    public function edit($id)
    {
        $this->action = 2;
        $record = OtroDebito::findOrFail($id);
        $this->selected_id = $id;
        $this->descripcion = $record->descripcion;
        $this->importe = $record->importe;
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
            OtroDebito::onlyTrashed()->find($id)->restore();
            $audit = Auditoria::create([
                'item_deleted_id' => $id,
                'tabla'           => 'Otros Débitos',
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
            'importe'     => 'required'
        ]);
        DB::begintransaction();
        try{
            if($this->selected_id > 0) {
                $existe = OtroDebito::where('descripcion', $this->descripcion)
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
                    session()->flash('info', 'El Item ya existe...');
                    $this->resetInput();
                    return;
                }
            }else {
                $existe = OtroDebito::where('descripcion', $this->descripcion)
                ->where('comercio_id', $this->comercioId)->withTrashed()->get();

                if($existe->count() && $existe[0]->deleted_at != null) {
                    $this->action = 1;
                    $this->recuperar_registro = 1;
                    $this->descripcion_soft_deleted = $existe[0]->descripcion;
                    $this->id_soft_deleted = $existe[0]->id;
                    return;
                }elseif($existe->count()) {
                    session()->flash('info', 'El Item ya existe...');
                    $this->resetInput();
                    return;
                }
            }
            if($this->selected_id <= 0) {
                $category =  OtroDebito::create([
                    'descripcion' => ucwords($this->descripcion),
                    'importe'     => $this->importe,
                    'comercio_id' => $this->comercioId            
                ]);
            }else {   
                $record = OtroDebito::find($this->selected_id);
                $record->update([
                    'descripcion' => ucwords($this->descripcion),
                    'importe'     => $this->importe,
                ]);
                $this->action = 1;              
            }
            if($this->selected_id) session()->flash('msg-ok', 'Item Actualizado');            
            else session()->flash('msg-ok', 'Item Creado');            
 
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
                $salsa = OtroDebito::find($id)->delete();
                $audit = Auditoria::create([
                    'item_deleted_id' => $id,
                    'tabla' => 'Otros Débitos',
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
