<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Plan;

class PlanesController extends Component
{
    //public properties
	public  $descripcion, $precio, $duracion, $estado = 'activo';  //campos de la tabla planes
    public  $selected_id, $search;                  //para búsquedas y fila seleccionada

    public function render()
    {
        if(strlen($this->search) > 0)
        {
            $info = Plan::where('descripcion', 'like', '%' .  $this->search . '%')
                    ->orderby('id','asc')->get();
            return view('livewire.admin.planes', [
                'info' =>$info
            ]);
        }
        else {
           return view('livewire.admin.planes', [
            'info' => plan::orderBy('id', 'asc')->get()
        ]);
       }

    }

    public function doAction($action)
    {
        $this->resetInput();
    }

    private function resetInput()
    {
        $this->descripcion = '';
        $this->precio = '';
        $this->duracion = '';
        $this->estado = 'activo';
        $this->selected_id = null;    
        $this->search = '';
    }

    public function edit($id)
    {
        $record = Plan::findOrFail($id);
        $this->selected_id = $id;
        $this->descripcion = $record->descripcion;
        $this->precio = $record->precio;
        $this->duracion = $record->duracion;
        $this->estado = $record->estado;
    }    
    
    //método para registrar y/o actualizar info
    public function StoreOrUpdate()
    { 
            //validación campos requeridos
        $this->validate([
            'descripcion' => 'required', //validamos que descripción no sea vacío o nullo y que tenga al menos 4 caracteres
            'precio' => 'required',
            'duracion' => 'required',
            'estado' => 'required'
        ]);

        //valida si existe otro plan con el mismo nombre (edicion de planes)
        if($this->selected_id > 0) {
            $existe = Plan::where('descripcion', $this->descripcion)
            ->where('id', '<>', $this->selected_id)
            ->select('descripcion')
            ->get();

            if( $existe->count() > 0) {
            session()->flash('msg-error', 'Ya existe el Plan');
            $this->resetInput();
            return;
            }
        }        
        else 
        {
            //valida si existe otro plan con el mismo nombre (nuevos registros)
            $existe = Plan::where('descripcion', $this->descripcion)
            ->select('descripcion')
            ->get();

            if($existe->count() > 0 ) {
            session()->flash('msg-error', 'Ya existe el Plan');
            $this->resetInput();
            return;
            }
        }
        if($this->selected_id <= 0) {
            //creamos el registro
            $plan = Plan::create([
                'descripcion' => mb_strtoupper($this->descripcion),            
                'precio' => $this->precio,
                'duracion' => $this->duracion,           
                'estado' => $this->estado         
            ]);
        }
        else 
        {   
            //buscamos el plan
            $record = Plan::find($this->selected_id);
            //actualizamos el registro
            $record->update([
            'descripcion' => mb_strtoupper($this->descripcion),
            'precio' => $this->precio,
            'duracion' => $this->duracion,           
            'estado' => $this->estado
            ]);              
        }
            //enviamos feedback al usuario
        if($this->selected_id) {
            session()->flash('message', 'Plan Actualizado');            
        }
        else {
            session()->flash('message', 'Plan Creado');            
        }
        //limpiamos las propiedades
        $this->resetInput();
    }
        //escuchar eventos y ejecutar acción solicitada
    protected $listeners = [
        'deleteRow'=>'destroy'        
    ];  
        //método para eliminar un registro dado
    public function destroy($id)
    {
        if ($id) { //si es un id válido
            $record = Plan::where('id', $id); //buscamos el registro
            $record->delete(); //eliminamos el registro
            $this->resetInput(); //limpiamos las propiedades
        }
    }
}
