<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Modulo;

class ModuloController extends Component
{
    public $selected_id, $search;  
    public $comercioId;

    public function render()
    {
        if(strlen($this->search) > 0)
        {
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
}
