<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Auditoria;
use App\Models\Categoria;
use App\Models\Rubro;
use DB;

class RubroController extends Component
{
    public $recuperar_registro, $descripcion_soft_deleted, $id_soft_deleted;
    public $comercioId, $selected_id, $search, $action;  
    public $descripcion, $mostrar;

    public function mount()
    {
        $this->recuperar_registro = 0;
        $this->action = 1;
        $this->mostrar = 'si';
    }

    public function render()
    {
        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');

        if(strlen($this->search) > 0) {
            $info = Rubro::where('descripcion', 'like', '%' .  $this->search . '%')
                        ->where('comercio_id', $this->comercioId)
                        ->select('rubros.*')
                        ->orderBy('descripcion')->get();
        } else {
            $info = Rubro::where('comercio_id', $this->comercioId)
                        ->select('rubros.*')
                        ->orderBy('descripcion')->get();
        }
        
        return view('livewire.rubros.component', [
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
        $this->descripcion = '';
        $this->mostrar = 'si';
        $this->selected_id = null;      
        $this->search      = '';
    }
    public function edit($id)
    {
        $this->action = 2;
        $record = Rubro::findOrFail($id);
        $this->selected_id = $id;
        $this->descripcion = $record->descripcion;
        $this->mostrar = $record->mostrar;
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
    public function StoreOrUpdate()
    { 
        $this->validate(['descripcion' => 'required']);

        if (!$this->verificarSiExiste()) {

            DB::begintransaction();
            try{
                if($this->selected_id > 0) {
                    $rubro = Rubro::find($this->selected_id);
                    $rubro->update([
                        'descripcion' => mb_strtoupper($this->descripcion),
                        'mostrar'     => $this->mostrar
                    ]);
                    $this->action = 1;                   
                } else {   
                        $rubro =  Rubro::create([
                        'descripcion' => mb_strtoupper($this->descripcion),
                        'mostrar'     => $this->mostrar,
                        'comercio_id' => $this->comercioId            
                    ]);          
                }
                if($this->selected_id) session()->flash('msg-ok', 'Rubro Actualizado');            
                else session()->flash('msg-ok', 'Rubro Creado');            

                DB::commit(); 
            }catch (Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
            }
        }
        $this->resetInput();
        return;
    } 
    public function verificarSiExiste()
    {
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
                return true;
            }elseif($existe->count()) {
                session()->flash('msg-ops', 'El Rubro ya existe...');
                $this->resetInput();
                return true;
            }
        }else {
            $existe = Rubro::where('descripcion', $this->descripcion)
            ->where('comercio_id', $this->comercioId)->withTrashed()->get();

            if($existe->count() && $existe[0]->deleted_at != null) {
                $this->action = 1;
                $this->recuperar_registro = 1;
                $this->descripcion_soft_deleted = $existe[0]->descripcion;
                $this->id_soft_deleted = $existe[0]->id;
                return true;
            }elseif($existe->count()) {
                session()->flash('msg-ops', 'El Rubro ya existe...');
                $this->resetInput();
                return true;
            }
        }
    }       
    public function deleteRow($id, $comentario)
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
