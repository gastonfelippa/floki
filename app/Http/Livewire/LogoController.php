<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Comercio;

class LogoController extends Component
{
    public $nombre, $logo, $nombreComercio;
    public $comercioId;
    
    public function render()
    {  
        $this->comercioId = session('idComercio');
        if(Auth()->user()->id !=1){
            $comercio = Comercio::where('id', $this->comercioId)->get();
            if($comercio->count()){  
                $this->nombre = $comercio[0]->nombre;
                $this->logo   = $comercio[0]->logo;
            }
        }else $this->nombreComercio = 'PANEL DE ADMINISTRACIÓN';

        return view('livewire.logo.component');
    }
}
