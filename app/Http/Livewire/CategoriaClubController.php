<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Auditoria;
use App\Models\CategoriaClub;
use App\Models\Socio;
use DB;

class CategoriaClubController extends Component
{
	public $descripcion, $edad_minima = null, $edad_maxima = null, $importe, $comercioId;
    public $selected_id, $search, $action = 1, $otorgarCategoriaSegunLaEdad = 0;
    public $descripcion_soft_deleted, $id_soft_deleted, $recuperar_registro = 0;  
    public $comercioTipo, $modComandas, $modDelivery;

    public function render()
    {
        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');
        
        $this->comercioTipo = session('tipoComercio');
        $this->modComandas = session('modComandas');
        $this->modDelivery = session('modDelivery');
        session(['facturaPendiente' => null]);  

        // $record = Comercio::find($this->comercioId);
        // $this->otorgarCategoriaSegunLaEdad = $record->otorgarCategoriaSegunLaEdad;


        if(strlen($this->search) > 0)
        {
            $info = CategoriaClub::where('descripcion', 'like', '%' .  $this->search . '%')
                        ->where('comercio_id', $this->comercioId) 
                        ->orderby('descripcion','desc')->get();
        }else {
            $info = CategoriaClub::orderBy('descripcion', 'asc')
                        ->where('comercio_id', $this->comercioId)->get();
        }
        return view('livewire.categoriasclub.component', [
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
        $this->edad_minima = null;
        $this->edad_maxima = null;
        $this->importe     = '';
        $this->selected_id = null;    
        $this->search      = '';
    }
    public function edit($id)
    {
        $this->action = 2;
        $record = CategoriaClub::findOrFail($id);
        $this->selected_id = $id;
        $this->descripcion = $record->descripcion;
        $this->edad_minima = $record->edad_minima;
        $this->edad_maxima = $record->edad_maxima;
        $this->importe     = $record->importe;
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
            CategoriaClub::onlyTrashed()->find($id)->restore();
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
        if ($this->otorgarCategoriaSegunLaEdad == 1){  
            $this->validate([                        
                'descripcion' => 'required',
                'edad_minima' => 'required|integer',
                'edad_maxima' => 'required|integer',
                'importe'     => 'required|integer'
            ]);
        }else{                                      
            $this->validate([                       
                'descripcion' => 'required',
                'importe'     => 'required|integer'
            ]);
        }
        
        DB::begintransaction();
        try{
            if($this->selected_id > 0) {
                $existe = CategoriaClub::where('descripcion', $this->descripcion)
                    ->where('id', '<>', $this->selected_id)
                    ->where('comercio_id', $this->comercioId)
                    ->withTrashed()->get();
                if($existe->count() && $existe[0]->deleted_at != null) {
                    session()->flash('info', 'La Categoría que desea modificar ya existe pero fué eliminada anteriormente, para recuperarla haga click en el botón "Recuperar registro"');
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
                $existe = CategoriaClub::where('descripcion', $this->descripcion)
                    ->where('comercio_id', $this->comercioId)->withTrashed()->get();

                if($existe->count() && $existe[0]->deleted_at != null) {
                    session()->flash('info', 'La Categoría que desea agregar ya existe pero fué eliminado anteriormente, para recuperarlo haga click en el botón "Recuperar registro"');
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
            if($this->selected_id > 0) {
                $record = CategoriaClub::find($this->selected_id);
                $record->update([
                    'descripcion' => mb_strtoupper($this->descripcion),            
                    'edad_minima' => $this->edad_minima,
                    'edad_maxima' => $this->edad_maxima,
                    'importe'     => $this->importe
                ]);
                $this->action = 1; 
            }else {  
                 $category =  CategoriaClub::create([
                    'descripcion' => mb_strtoupper($this->descripcion),            
                    'edad_minima' => $this->edad_minima,
                    'edad_maxima' => $this->edad_maxima,
                    'importe'     => $this->importe,
                    'comercio_id' => $this->comercioId            
                ]);           
            }
            if($this->selected_id > 0) session()->flash('msg-ok', 'Categoria Actualizada');            
            else session()->flash('msg-ok', 'Categoria Creada');            
 
            DB::commit(); 
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        $this->resetInput();
        return;
    }        
    public function destroy($id, $comentario)
    {
        if ($id) {
            $record = Socio::where('categoria_id', $id)->get();
            if(!$record->count()){
                DB::begintransaction();
                try{
                    $categoria = CategoriaClub::find($id)->delete();
                    $audit = Auditoria::create([
                        'item_deleted_id' => $id,
                        'tabla'           => 'Categorías',
                        'user_delete_id'  => auth()->user()->id,
                        'comentario'      => $comentario,
                        'comercio_id'     => $this->comercioId
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
