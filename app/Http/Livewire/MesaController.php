<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Mesa;
use App\Models\User;

class MesaController extends Component
{
    public $comercioId, $action = 1;
    
    public function render()
    {
        $this->comercioId = session('idComercio');
        session(['facturaPendiente' => null]); 
        
        $mesas = Mesa::where('comercio_id', $this->comercioId)->get();
        $mozos = User::join('usuario_comercio as uc', 'uc.usuario_id', 'users.id')
        ->where('uc.comercio_id', $this->comercioId)->select('users.*')->orderBy('apellido')->get();

        return view('livewire.mesas.component', ['mesas' => $mesas, 'mozos' => $mozos]);
    }
    protected $listeners = [
        'abrirMesa'      => 'abrirMesa',
        'abrirMesaNueva' => 'abrirMesaNueva',
        'asignarMozo'    => 'asignarMozo'
    ];
    public function doAction($action)
    {
        $this->action = $action;
    }
    public function abrirMesa($data)
    {
        $info = json_decode($data);
        $mesaDesc = $info->mesa_desc;
        $buscar_mesa = Mesa::where('descripcion', $mesaDesc)->get();
        if($buscar_mesa->count() > 0){
            $mesaId = $buscar_mesa[0]->id;
            session(['idMesa' => $mesaId]);
            if($buscar_mesa[0]->estado == 'Disponible'){}
            else return redirect()->to('/facturasbar');
        }else session()->flash('message', 'La mesa ingresada no existe');
    }
    public function abrirMesaNueva($data)
    {
        $info = json_decode($data);
        $mesaDesc = $info->mesa_desc;
        $mozo = $info->mozo;
        session(['idMozo' => $mozo]);
        $buscar_mesa = Mesa::where('descripcion', $mesaDesc)->get();
        if($buscar_mesa->count() > 0){
            $mesaId = $buscar_mesa[0]->id;
            session(['idMesa' => $mesaId]);
            return redirect()->to('/facturasbar');
        }else session()->flash('message', 'La mesa ingresada no existe');
    }
    public function asignarMozo($data)
    {
        $info = json_decode($data);
        //$mesaId = $info->mesa_id;

        $mozoId = $info->mozo_id;
        //session(['idMesa' => $mesaId]);
        session(['idMozo' => $mozoId]);

        return redirect()->to('/facturasbar');
    }
}