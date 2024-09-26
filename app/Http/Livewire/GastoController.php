<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Auditoria;
use App\Models\Gasto;
use App\Models\CategoriaGasto;
use DB;

class GastoController extends Component
{  
	public $descripcion, $categoria = 'Elegir', $tipo_gasto = 1;            
    public $selected_id, $search; 
    public $comercioId, $action = 1;
    public $recuperar_registro = 0, $descripcion_soft_deleted, $id_soft_deleted;

    public function render()
    {
        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');
        session(['facturaPendiente' => null]);  

        $categorias = CategoriaGasto::where('comercio_id', $this->comercioId)->get();

        if(strlen($this->search) > 0)
        {
            $info = Gasto::where('descripcion', 'like', '%' .  $this->search . '%')
                        ->where('comercio_id', $this->comercioId)
                        ->orderby('descripcion','desc')->get();
        }else {
            $info = Gasto::orderBy('descripcion', 'asc')
                        ->where('comercio_id', $this->comercioId)->get();
        }                
        return view('livewire.gastos.component', [
            'info' =>$info,
            'categorias' =>$categorias
        ]);
    }
    protected $listeners = [
        'StoreOrUpdate'   => 'StoreOrUpdate',       
        'createFromModal' => 'createFromModal',
        'deleteRow'       =>'destroy'
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
        $this->search      = '';
        $this->categoria   = 'Elegir';
        $this->tipo_gasto = 1;
    }
    public function edit($id)
    {
        $record = Gasto::findOrFail($id);
        $this->selected_id = $id;
        $this->descripcion = $record->descripcion;
        $this->categoria = $record->categoria_id;
        
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
            Gasto::onlyTrashed()->find($id)->restore();            
            $audit = Auditoria::create([
                'item_deleted_id' => $id,
                'tabla'           => 'Egresos',
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
            'descripcion' => 'required'
        ]);  

        DB::begintransaction();
        try{
            if($this->selected_id > 0) {
                $existe = Gasto::join('categoria_gastos as c', 'c.id', 'gastos.categoria_id')
                    ->where('gastos.descripcion', $this->descripcion)
                    ->where('gastos.id', '<>', $this->selected_id)
                    ->where('gastos.comercio_id', $this->comercioId)
                    ->select('gastos.*', 'c.descripcion as c_descripcion')
                    ->withTrashed()->get();
                if($existe->count() > 0 && $existe[0]->deleted_at != null) {
                    // session()->flash('msg-error', 'El Empleado que desea agregar ya existe en el sistema pero fué eliminado anteriormente, para recuperarlo haga click en el botón "Recuperar registro"');
                    $this->action = 1;
                    $this->recuperar_registro = 1;
                    $this->descripcion_soft_deleted = $existe[0]->descripcion . ' - ' . $existe[0]->c_descripcion;
                    $this->id_soft_deleted = $existe[0]->id;
                    return;
                }elseif($existe->count() > 0) {
                    session()->flash('msg-ops','El registro no se grabó... el Gasto ya existe...');
                    $this->resetInput();
                    return;
                }
            }else {
                $existe = Gasto::join('categoria_gastos as c', 'c.id', 'gastos.categoria_id')
                    ->where('gastos.descripcion', $this->descripcion)
                    ->where('gastos.comercio_id', $this->comercioId)
                    ->select('gastos.*', 'c.descripcion as c_descripcion')
                    ->withTrashed()->get();
                if($existe->count() > 0 && $existe[0]->deleted_at != null) {
                    // session()->flash('msg-error', 'El Empleado que desea agregar ya existe en el sistema pero fué eliminado anteriormente, para recuperarlo haga click en el botón "Recuperar registro"');
                    $this->action = 1;
                    $this->recuperar_registro = 1;
                    $this->descripcion_soft_deleted = $existe[0]->descripcion . ' - ' . $existe[0]->c_descripcion;
                    $this->id_soft_deleted = $existe[0]->id;
                    return;
                }elseif($existe->count() > 0) {
                    session()->flash('msg-ops','El registro no se grabó... el Gasto ya existe...');
                    $this->resetInput();
                    return;
                }
            }
            if($this->selected_id <= 0) {
                $gasto =  Gasto::create([
                    'descripcion'  => mb_strtoupper($this->descripcion),
                    'categoria_id' => $this->categoria,
                    'comercio_id'  => $this->comercioId
                ]);
            }else {   
                $gasto = Gasto::find($this->selected_id);
                $gasto->update([
                    'descripcion' => mb_strtoupper($this->descripcion),
                    'categoria_id' => $this->categoria
                ]);                
                $this->action = 1;             
            }
            if($this->selected_id) session()->flash('msg-ok', 'Gasto Actualizado');            
            else session()->flash('msg-ok', 'Gasto Creado');
            
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
        $existe = CategoriaGasto::where('descripcion', ucwords($data->descripcion))
                ->where('comercio_id', $this->comercioId)->get();  
        if($existe->count() > 0 ) {
            session()->flash('info', 'La Categoría de Gasto ingresada ya existe!!!');
            return;
        }else{ 
            DB::begintransaction();
            try{   
                CategoriaGasto::create([
                    'descripcion' => ucwords($data->descripcion),
                    'tipo'        => $data->tipo,
                    'comercio_id' => $this->comercioId
                ]);
                session()->flash('msg-ok', 'Categoría creada exitosamente!!!'); 
                DB::commit();               
            }catch (\Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se creó...');
            }
        }
    }
    public function destroy($id, $comentario)
    {
        if ($id) {
            DB::begintransaction();
            try{
                $gasto = Gasto::find($id)->delete();
                $audit = Auditoria::create([
                    'item_deleted_id' => $id,
                    'tabla'           => 'Egresos',
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
