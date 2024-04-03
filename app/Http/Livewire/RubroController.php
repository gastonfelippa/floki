<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Auditoria;
use App\Models\Categoria;
use App\Models\Rubro;
use DB;

class RubroController extends Component
{
    public $descripcion, $descripcion_soft_deleted, $id_soft_deleted;
    public $selected_id, $search, $action = 1, $recuperar_registro = 0;  
    public $mostrar_al_vender = 'si', $comercioId;

    public function render()
    {
        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');

        if(strlen($this->search) > 0)
        {
            $info = Rubro::where('descripcion', 'like', '%' .  $this->search . '%')
                        ->where('comercio_id', $this->comercioId) 
                        ->orderby('descripcion','desc')->get();
        }else {
            $info = Rubro::orderBy('descripcion', 'asc')
                        ->where('comercio_id', $this->comercioId)->get();
        }
        return view('livewire.rubros.component', [
            'info' => $info
        ]);
    }    
    protected $listeners = [
        'StoreOrUpdate' => 'StoreOrUpdate',
        'deleteRow'     =>'destroy'        
    ];
    public function doAction($action)
    {
        $this->action = $action;
        $this->resetInput();
    }
    private function resetInput()
    {
        $this->descripcion       = '';
        $this->mostrar_al_vender = 'si';
        $this->selected_id       = null;    
        $this->search            = '';
    }
    public function edit($id)
    {
        $this->action = 2;
        $record = Rubro::findOrFail($id);
        $this->selected_id = $id;
        $this->descripcion = $record->descripcion;
        if($record->mostrar_al_vender == 'si') $this->mostrar_al_vender = 'si';
        else $this->mostrar_al_vender = 'no';
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
            Rubro::onlyTrashed()->find($id)->restore();
            $audit = Auditoria::create([
                'item_deleted_id' => $id,
                'tabla'           => 'Rubros',
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
    public function StoreOrUpdate($mostrar)
    { 
        $this->validate(['descripcion' => 'required']);
        
        DB::begintransaction();
        try{
            if($this->selected_id > 0) {
                $existe = Rubro::where('descripcion', $this->descripcion)
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
                    session()->flash('info', 'El Rubro ya existe...');
                    $this->resetInput();
                    return;
                }
            }else {
                $existe = Rubro::where('descripcion', $this->descripcion)
                ->where('comercio_id', $this->comercioId)->withTrashed()->get();

                if($existe->count() && $existe[0]->deleted_at != null) {
                    $this->action = 1;
                    $this->recuperar_registro = 1;
                    $this->descripcion_soft_deleted = $existe[0]->descripcion;
                    $this->id_soft_deleted = $existe[0]->id;
                    return;
                }elseif($existe->count()) {
                    session()->flash('info', 'El Rubro ya existe...');
                    $this->resetInput();
                    return;
                }
            }
            if($this->selected_id <= 0) {
                $category =  Rubro::create([
                    'descripcion'       => mb_strtoupper($this->descripcion),
                    'mostrar_al_vender' => $mostrar, 
                    'comercio_id'       => $this->comercioId            
                ]);
            }else {   
                $record = Rubro::find($this->selected_id);
                $record->update([
                    'descripcion'       => mb_strtoupper($this->descripcion),
                    'mostrar_al_vender' => $mostrar
                ]);
                $this->action = 1;              
            }
            if($this->selected_id) session()->flash('msg-ok', 'Rubro Actualizado');            
            else session()->flash('msg-ok', 'Rubro Creado');            

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
            $record = Categoria::where('rubro_id', $id)->get();
            if(!$record->count()){
                DB::begintransaction();
                try{
                    $rubro = Rubro::find($id)->delete();
                    $audit = Auditoria::create([
                        'item_deleted_id' => $id,
                        'tabla' => 'Rubros',
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
