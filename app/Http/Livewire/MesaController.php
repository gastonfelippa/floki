<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Mesa;
use App\Models\User;

class MesaController extends Component
{
    public $comercioId;
    
    public function render()
    {
        $this->comercioId = session('idComercio');
        
        $mesas = Mesa::where('comercio_id', $this->comercioId)->get();
        $mozos = User::join('usuario_comercio as uc', 'uc.usuario_id', 'users.id')
            ->where('uc.comercio_id', $this->comercioId)->select('users.*')->orderBy('apellido')->get();
        return view('livewire.mesas.component', ['mesas' => $mesas, 'mozos' => $mozos]);
    }
    protected $listeners = [
        'abrirMesa' => 'abrirMesa'
    ];
    public function abrirMesa($data)
    {
        $info = json_decode($data);
        $mesa = $info->mesa_id;
        $mozo = $info->mozo_id;
        session(['idMesa' => $mesa]);
        session(['idMozo' => $mozo]);
        return redirect()->to('/facturas');
    }
}
