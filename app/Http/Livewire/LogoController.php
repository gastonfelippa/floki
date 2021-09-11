<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Comercio;
use App\Models\UsuarioComercio;
use DB;

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
            // $nombreComercio = UsuarioComercio::leftjoin('users as u','u.id','usuario_comercio.usuario_id')
            //     ->leftjoin('comercios as c','c.id','usuario_comercio.comercio_id')
            //     ->select('c.nombre')
            //     ->where('usuario_comercio.usuario_id', Auth()->user()->id)->get();
            // if($nombreComercio->count()) $this->nombreComercio = $nombreComercio[0]->nombre;
        }else $this->nombreComercio = 'PANEL DE ADMINISTRACIÓN';

        return view('livewire.logo.component');
    }
}
