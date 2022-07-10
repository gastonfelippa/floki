<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Mesa;

class ReservasEstadoMesasController extends Component
{
    public $comercioId, $estadoMesa = "1", $info, $tab="Interior", $factura_id = 7;

    public function render()
    {
        $this->comercioId = session('idComercio');

        switch ($this->estadoMesa) {
            case '1': //todas
                $this->info = Mesa::join('sectores as s', 's.id', 'mesas.sector_id')
                    ->select('mesas.*', 's.descripcion as mesa')
                    ->where('mesas.comercio_id', $this->comercioId)
                    ->where('s.descripcion', $this->tab)
                    ->orderBy('mesas.descripcion', 'asc')->get();
                break;
             case '2': //disponibles
                $this->info = Mesa::join('sectores as s', 's.id', 'mesas.sector_id')
                    ->select('mesas.*', 's.descripcion as mesa')
                    ->where('mesas.comercio_id', $this->comercioId)
                    ->where('s.descripcion', $this->tab)
                    ->where('mesas.estado', 'Disponible')
                    ->orderBy('mesas.descripcion', 'asc')->get();
                break;
            case '3': //ocupadas
                $this->info = Mesa::join('sectores as s', 's.id', 'mesas.sector_id')
                    ->select('mesas.*', 's.descripcion as mesa')
                    ->where('mesas.comercio_id', $this->comercioId)
                    ->where('s.descripcion', $this->tab)
                    ->where('mesas.estado', 'Ocupada')
                    ->orderBy('mesas.descripcion', 'asc')->get();
                break;
            case '4': //c/factura 
                $this->info = Mesa::join('sectores as s', 's.id', 'mesas.sector_id')
                    ->select('mesas.*', 's.descripcion as mesa')
                    ->where('mesas.comercio_id', $this->comercioId)
                    ->where('s.descripcion', $this->tab)
                    ->where('mesas.estado', 'C/factura')
                    ->orderBy('mesas.descripcion', 'asc')->get();
                break;
            case '5': //canceladas 
                $this->info = Mesa::join('sectores as s', 's.id', 'mesas.sector_id')
                    ->select('mesas.*', 's.descripcion as mesa')
                    ->where('mesas.comercio_id', $this->comercioId)
                    ->where('s.descripcion', $this->tab)
                    ->where('mesas.estado', 'Cancelada')
                    ->orderBy('mesas.descripcion', 'asc')->get();
                break;
            case '6': //reservadas
                $this->info = Mesa::join('sectores as s', 's.id', 'mesas.sector_id')
                    ->select('mesas.*', 's.descripcion as mesa')
                    ->where('mesas.comercio_id', $this->comercioId)
                    ->where('s.descripcion', $this->tab)
                    ->where('mesas.estado', 'Reservada')
                    ->orderBy('mesas.descripcion', 'asc')->get();
                break;
            default:
        }
        return view('livewire.reservas-estado-mesas.component' , ['info' => $this->info]);
    }
    protected $listeners = ['abrirMesa'   => 'abrirMesa'];

    public function cambiarSector()
    {
        if($this->tab == "Interior") $this->tab = "Exterior";
        else $this->tab = "Interior";
    }
    public function agregarReserva()
    {

    }
    public function abrirMesa($data)
    {
        $info = json_decode($data);
        $buscar_mesa = Mesa::find($info);
    
        if($buscar_mesa->count() > 0){
            $mesaId = $buscar_mesa->id;
            session(['idMesa' => $mesaId]);
            
        return redirect()->to('/abrir_mesa');
        }
    }
}
