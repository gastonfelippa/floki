<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Modulo;

class ModuloController extends Component
{
    public $selected_id, $search, $action = 1; 
    public $comercio, $modViandas, $modComandas, $modDelivery, $modConsignaciones, $modClubes; 
    public $comercioId;

    public function render()
    {
        if(strlen($this->search) > 0){
            $info = Modulo::join('comercios as c', 'c.id', 'modulos.comercio_id')
            ->where('c.nombre', 'like', '%' .  $this->search . '%')
            ->select('modulos.*', 'c.nombre')->get();
        }else {
            $info = Modulo::join('comercios as c', 'c.id', 'modulos.comercio_id')
            ->select('modulos.*', 'c.nombre')->get();
        }

        return view('livewire.modulos.component', [
            'info' => $info
        ]);
    }
    public function doAction($action)
    {
        $this->action = $action;
        $this->resetInput();
    }
    protected $listeners = [
        'AsignarModulos' => 'AsignarModulos'
    ]; 
    public function edit($id)
    {
        $this->action = 2;
        $record = Modulo::where('modulos.id',$id)
            ->join('comercios as c', 'c.id', 'modulos.comercio_id')
            ->select('modulos.*', 'c.nombre')->first();
      
        $this->selected_id       = $id;
        $this->comercio          = $record->nombre;
        $this->modViandas        = $record->modViandas;
        $this->modComandas       = $record->modComandas;
        $this->modDelivery       = $record->modDelivery;
        $this->modConsignaciones = $record->modConsignaciones;
        $this->modClubes         = $record->modClubes;
    }
    public function AsignarModulos($modulosList)
    {
        $record = Modulo::find($this->selected_id);
        if (in_array('modViandas', $modulosList)) $record->update([ 'modViandas' => '1' ]);
        else $record->update([ 'modViandas' => '0' ]);
        if (in_array('modComandas', $modulosList)) $record->update([ 'modComandas' => '1' ]);
        else $record->update([ 'modComandas' => '0' ]);
        if (in_array('modDelivery', $modulosList)) $record->update([ 'modDelivery' => '1' ]);
        else $record->update([ 'modDelivery' => '0' ]);
        if (in_array('modConsignaciones', $modulosList)) $record->update([ 'modConsignaciones' => '1' ]);
        else $record->update([ 'modConsignaciones' => '0' ]);
        if (in_array('modClubes', $modulosList)) $record->update([ 'modClubes' => '1' ]);
        else $record->update([ 'modClubes' => '0' ]);

        $this->action = 1;
        session()->flash('msg-ok', 'Módulos asignados correctamente');
    }
}
