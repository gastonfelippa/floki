<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Auditoria;
use App\Models\Mesa;
use App\Models\Sector;
use DB;

class MesaController extends Component
{
    public $comercioId, $action = 1, $selected_id, $search = null;
    public $recuperar_registro = 0, $descripcion_soft_deleted, $id_soft_deleted;
    public $descripcion, $capacidad, $sector = 'Elegir', $estado, $tipo_gasto;

    public function render()
    {
        $this->comercioId = session('idComercio');

        $sectores = Sector::where('comercio_id', $this->comercioId)->get();

        if(strlen($this->search)) {
            $info = Mesa::where('comercio_id', $this->comercioId)
                ->where('descripcion', 'like', $this->search)
                ->orWhere('comercio_id', $this->comercioId)
                ->where('estado', 'like', '%' . $this->search .'%')->get();
        }else $info = Mesa::where('comercio_id', $this->comercioId)->get();
        
        return view('livewire.mesas.component', ['info' => $info, 'sectores' => $sectores]);
    }
    protected $listeners = [
        'deleteRow'=>'destroy',
        'createFromModal' => 'createFromModal'       
    ]; 
    public function doAction($action)
    {
        $this->action = $action;
        $this->resetInput();
    }
    private function resetInput()
    {
        $this->descripcion = '';
        $this->selected_id = null;    
        $this->search      = null;
        $this->sector      = 'Elegir';
        $this->capacidad   = '';
        $this->estado      = '';
        $this->tipo_gasto  = 1;
    }
    public function edit($id)
    {
        $record = Mesa::findOrFail($id);
        $this->selected_id = $id;
        $this->descripcion = $record->descripcion;
        $this->capacidad   = $record->capacidad;
        $this->estado      = $record->estado;
        $this->sector      = $record->sector_id;
        
        $this->action = 2;
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
            Mesa::onlyTrashed()->find($id)->restore();            
            $audit = Auditoria::create([
                'item_deleted_id' => $id,
                'tabla'           => 'Mesas',
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
            'sector'      => 'not_in:Elegir',
            'descripcion' => 'required|numeric',
            'capacidad'   => 'required|numeric'
        ]);  

        DB::begintransaction();
        try{
            if($this->selected_id > 0) {
                $existe = Mesa::join('sectores as s', 's.id', 'mesas.sector_id')
                    ->where('mesas.descripcion', $this->descripcion)
                    ->where('mesas.id', '<>', $this->selected_id)
                    ->where('mesas.comercio_id', $this->comercioId)
                    ->select('mesas.*', 's.descripcion as s_descripcion')
                    ->withTrashed()->get();
                if($existe->count() > 0 && $existe[0]->deleted_at != null) {
                    $this->action = 1;
                    $this->recuperar_registro = 1;
                    $this->descripcion_soft_deleted = $existe[0]->descripcion . ' - ' . $existe[0]->s_descripcion;
                    $this->id_soft_deleted = $existe[0]->id;
                    return;
                }elseif($existe->count() > 0) {
                    session()->flash('msg-ops','El registro no se grabó... la Mesa ' . $this->descripcion . ' ya existe...');
                    $this->resetInput();
                    return;
                }
            }else {
                $existe = Mesa::where('descripcion', $this->descripcion)
                    ->where('comercio_id', $this->comercioId)
                    ->select('*')
                    ->withTrashed()->get();
                if($existe->count() > 0 && $existe[0]->deleted_at != null) {
                    $this->action = 1;
                    $this->recuperar_registro = 1;
                    $this->descripcion_soft_deleted = $existe[0]->descripcion;
                    $this->id_soft_deleted = $existe[0]->id;
                    return;
                }elseif($existe->count() > 0) {
                    session()->flash('msg-ops','El registro no se grabó... la Mesa  ' . $this->descripcion . ' ya existe...');
                    $this->resetInput();
                    return;
                }
            }
            if($this->selected_id > 0) {
                $mesa = Mesa::find($this->selected_id);
                $mesa->update([
                    'descripcion' => $this->descripcion,
                    'capacidad'   => $this->capacidad,
                    'sector_id'   => $this->sector
                ]);                
                $this->action = 1;   
            }else {   
                $mesa =  Mesa::create([
                    'descripcion' => $this->descripcion,
                    'capacidad'   => $this->capacidad,
                    'sector_id'   => $this->sector,
                    'comercio_id' => $this->comercioId
                ]);           
            }
            if($this->selected_id) session()->flash('msg-ok', 'Mesa Actualizada');            
            else session()->flash('msg-ok', 'Mesa Creada');
            
            DB::commit();               
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        $this->resetInput();
        return;
    }
    public function createFromModal($info)
    {
        $data = json_decode($info);
        DB::begintransaction();
        try{
            $existe = Sector::where('descripcion', $data->descripcion)
                ->where('comercio_id', $this->comercioId)
                ->select('*')
                ->withTrashed()->get();
            if($existe->count() > 0 && $existe[0]->deleted_at != null) {
                $this->action = 1;
                $this->recuperar_registro = 1;
                $this->descripcion_soft_deleted = $existe[0]->descripcion;
                $this->id_soft_deleted = $existe[0]->id;
                return;
            }elseif($existe->count() > 0) {
                session()->flash('msg-ops','El registro no se grabó... el Sector ' . $data->descripcion . ' ya existe...');
                $this->resetInput();
                return;
            }   
            Sector::create([
                'descripcion' => ucwords($data->descripcion),
                'comercio_id' => $this->comercioId
            ]);
            session()->flash('msg-ok', 'Sector creado exitosamente!!!'); 
            DB::commit();               
        }catch (\Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se creó...');
        }
    }
    public function destroy($id, $comentario)
    {
        if ($id) {
            DB::begintransaction();
            try{
                $gasto = Mesa::find($id)->delete();
                $audit = Auditoria::create([
                    'item_deleted_id' => $id,
                    'tabla'           => 'Mesas',
                    'estado'          => '0',
                    'user_delete_id'  => auth()->user()->id,
                    'comentario'      => $comentario,
                    'comercio_id'     => $this->comercioId
                ]);
                session()->flash('msg-ok', 'Registro eliminado con éxito!!');
                DB::commit();               
            }catch (\Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se eliminó...');
            }
            $this->resetInput();
            return;
        }
    } 
}
