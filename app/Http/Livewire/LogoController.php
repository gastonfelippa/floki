<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Empresa;
use App\Models\UsuarioComercio;
use DB;

class LogoController extends Component
{
    public $nombre, $logo, $nombreComercio;
    
    public function render()
    {  
        if(Auth()->user()->id !=1){
            $empresa = Empresa::all();
            if($empresa->count()){  
                $this->nombre = $empresa[0]->nombre;
                $this->logo = $empresa[0]->logo;
            }
            $nombreComercio = UsuarioComercio::leftjoin('users as u','u.id','usuario_comercio.usuario_id')
                ->leftjoin('comercios as c','c.id','usuario_comercio.comercio_id')
                ->select('c.nombre')
                ->where('usuario_comercio.usuario_id', Auth()->user()->id)->get();
            if($nombreComercio->count()) $this->nombreComercio = $nombreComercio[0]->nombre;
        }else $this->nombreComercio = 'PANEL DE ADMINISTRACIÓN';

        return view('livewire.logo.component');
    }
}
