<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Mesa;
use App\Models\User;

class AbrirMesaController extends Component
{
    public $comercioId, $action = 1;
    
    public function render()
    {
        $this->comercioId = session('idComercio');
        session(['facturaPendiente' => null]); 
        session(['idMozo' => null]);
        session(['idMesa' => null]);
    
    
        if(session()->has('idMesa')){
            $mesa = Mesa::find(session("idMesa"));
            $this->abrirMesa(null, $mesa->descripcion); 
        } 

        $mesas = Mesa::where('comercio_id', $this->comercioId)->get();
        $mozos = User::join('usuario_comercio as uc', 'uc.usuario_id', 'users.id')
            ->where('uc.comercio_id', $this->comercioId)->select('users.*')->orderBy('apellido')->get();

        return view('livewire.abrir_mesa.component', ['mesas' => $mesas, 'mozos' => $mozos]);
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
    public function abrirMesa($data, $mesa)
    {
        if($data){
            $info = json_decode($data); 
            $mesaDesc = $info->mesa_desc;  
        }else $mesaDesc = $mesa;

        if($mesaDesc == 'd' || $mesaDesc == 'D'){
            session(['idMesa' => $mesaDesc]);
            return redirect()->to('/facturasbar');
        }else{
            $buscar_mesa = Mesa::where('descripcion', $mesaDesc)
                ->where('comercio_id', $this->comercioId)->get();
            if($buscar_mesa->count()){
                $mesaId = $buscar_mesa[0]->id;
                session(['idMesa' => $mesaId]);
                if($buscar_mesa[0]->estado == 'Disponible' || $buscar_mesa[0]->estado == 'Reservada'){
                    $this->emit('mesa', $mesaId);
                }
                else return redirect()->to('/facturasbar');
            }else session()->flash('message', 'La mesa ingresada no existe');            
        }

    }
    public function abrirMesaNueva($data)
    {
        $info = json_decode($data);
        $mesaDesc = $info->mesa_desc;
        $mozo = $info->mozo;
        session(['idMozo' => $mozo]);
        $buscar_mesa = Mesa::where('descripcion', $mesaDesc)
            ->where('comercio_id', $this->comercioId)->get();
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