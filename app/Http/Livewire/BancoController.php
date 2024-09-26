<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Auditoria;
use App\Models\Banco;
use App\Models\Cheque;
use App\Models\Comercio;
use DB;

class BancoController extends Component
{
    public $descripcion, $sucursal, $descripcion_soft_deleted, $id_soft_deleted;
    public $selected_id, $search, $action = 1, $recuperar_registro = 0;  
    public $comercioId;

    public function render()
    {
        $this->comercioId = session('idComercio');

        if(strlen($this->search) > 0){
            $info = Banco::where('descripcion', 'like', '%' .  $this->search . '%')
                        ->where('comercio_id', $this->comercioId) 
                        ->orWhere('sucursal', 'like', '%' .  $this->search . '%')
                        ->where('comercio_id', $this->comercioId) 
                        ->orderby('descripcion')->get();
        }else {
            $info = Banco::orderBy('descripcion')->where('comercio_id', $this->comercioId)->get();
        }
        return view('livewire.bancos.component', [
            'info' => $info
        ]);
    }  

    protected $listeners = ['deleteRow'=>'destroy'];

    public function doAction($action)
    {
        $this->action = $action;
        $this->resetInput();
    }
    private function resetInput()
    {
        $this->descripcion = '';
        $this->sucursal = '';
        $this->selected_id = null;    
        $this->search = '';
    }
    public function edit($id)
    {
        $this->action = 2;
        $record = Banco::findOrFail($id);
        $this->selected_id = $id;
        $this->descripcion = $record->descripcion;
        $this->sucursal    = $record->sucursal;
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
            Banco::onlyTrashed()->find($id)->restore();
            $audit = Auditoria::create([
                'item_deleted_id' => $id,
                'tabla'           => 'Bancos',
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
        $this->validate(['descripcion' => 'required']);
        $this->validate(['sucursal' => 'required']);
        
        DB::begintransaction();
        try{
            if($this->selected_id > 0) {
                $existe = Banco::where('descripcion', $this->descripcion)
                    ->where('sucursal', $this->sucursal)
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
                    session()->flash('info', 'El Banco ya existe...');
                    $this->resetInput();
                    return;
                }
            }else {
                $existe = Banco::where('descripcion', $this->descripcion)
                    ->where('sucursal', $this->sucursal)
                    ->where('comercio_id', $this->comercioId)->withTrashed()->get();

                if($existe->count() && $existe[0]->deleted_at != null) {
                    $this->action = 1;
                    $this->recuperar_registro = 1;
                    $this->descripcion_soft_deleted = $existe[0]->descripcion;
                    $this->id_soft_deleted = $existe[0]->id;
                    return;
                }elseif($existe->count()) {
                    session()->flash('info', 'El Banco ya existe...');
                    $this->resetInput();
                    return;
                }
            }
            if($this->selected_id <= 0) {
                $category =  Banco::create([
                    'descripcion' => mb_strtoupper($this->descripcion), 
                    'sucursal'    => ucwords($this->sucursal), 
                    'comercio_id' => $this->comercioId            
                ]);
            }else {   
                $record = Banco::find($this->selected_id);
                $record->update([
                    'descripcion' => mb_strtoupper($this->descripcion),
                    'sucursal'    => ucwords($this->sucursal)
                ]);
                $this->action = 1;              
            }
            if($this->selected_id) session()->flash('msg-ok', 'Banco Actualizado');            
            else session()->flash('msg-ok', 'Banco Creado');            

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
            $record = Cheque::where('banco_id', $id)->get();
            if(!$record->count()){
                DB::begintransaction();
                try{
                    $banco = Banco::find($id)->delete();
                    $audit = Auditoria::create([
                        'item_deleted_id' => $id,
                        'tabla' => 'Bancos',
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
