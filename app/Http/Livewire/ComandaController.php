<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Comanda;
use App\Models\Detcomanda;
use App\Models\SectorComanda;
use Carbon\Carbon;
use DB;

class ComandaController extends Component
{
    public $infoEnEspera, $infoDetEnEspera;
    public $infoProcesando, $infoDetProcesando;
    public $infoTerminado, $infoDetTerminado;
    public $comSelEnEspera = null, $comSelProcesando = null, $comSelTerminado = null;
    public $dato= null, $sectorComanda = null, $comercioId;
    public $vista = null, $posicion = 0, $sonido;
    public $contadorEE, $contadorP, $contadorT;

    public function render()
    { 
        $this->contadorEE = -1;
        $this->contadorP  = -1;
        $this->contadorT  = -1;

        $this->comercioId = session('idComercio');

        //if($this->sonido != 1) $this->sonido = null;

        if(!$this->sectorComanda){
            $sectorComanda = SectorComanda::where('descripcion', 'like', 'Cocina')
                ->where('comercio_id', $this->comercioId)->select('id')->first();
                $this->sectorComanda = $sectorComanda->id;
        }

        if(!$this->vista) $this->vista = '1';
        
        $this->infoEnEspera = Comanda::join('facturas as f', 'f.id', 'comandas.factura_id')
            ->join('users as u', 'u.id', 'f.mozo_id')
            ->join('mesas as m', 'm.id', 'f.mesa_id')
            ->join('sector_comandas as sc', 'sc.id', 'comandas.sectorcomanda_id')
            ->where('f.estado', 'abierta')
            ->where('comandas.estado', 'en espera')
            ->where('comandas.sectorcomanda_id', $this->sectorComanda)
            ->where('sc.comercio_id', $this->comercioId)
            ->select('comandas.*', 'u.name', 'm.descripcion', DB::RAW("'' as demora"))
            ->orderBy('comandas.sent_at', 'asc')->get(); 
        foreach($this->infoEnEspera as $i){
            $this->contadorEE = $this->contadorEE + 1;
            $date = Carbon::parse($i->sent_at);
            $now = Carbon::now();
            $diff = $date->diff($now);
            if ($diff->format('%h') > 0) $diff = $diff->format("%h h %i min");
            else $diff = $diff->format("%i min");
            $i->demora = $diff;
        } 
        $this->infoDetEnEspera = Detcomanda::join('comandas as c', 'c.id', 'detcomandas.comanda_id')
        ->where('c.estado', 'en espera')->orderBy('descripcion')->get(); 
    
        $this->infoProcesando = Comanda::join('facturas as f', 'f.id', 'comandas.factura_id')
            ->join('users as u', 'u.id', 'f.mozo_id')
            ->join('mesas as m', 'm.id', 'f.mesa_id')
            ->where('f.estado', 'abierta')
            ->where('comandas.estado', 'procesando')
            ->where('sectorcomanda_id', $this->sectorComanda)
            ->select('comandas.*', 'u.name', 'm.descripcion', DB::RAW("'' as demora"))
            ->orderBy('comandas.sent_at')->get();
        foreach($this->infoProcesando as $i){
            $this->contadorP = $this->contadorP + 1;
            $date = Carbon::parse($i->sent_at);
            $now = Carbon::now();
            $diff = $date->diff($now);
            if ($diff->format('%h') > 0) $diff = $diff->format("%h h %i min");
            else $diff = $diff->format("%i min");
            $i->demora = $diff;
        }            
        $this->infoDetProcesando = Detcomanda::join('comandas as c', 'c.id', 'detcomandas.comanda_id')
            ->where('c.estado', 'procesando')->orderBy('descripcion')->get();


        $this->infoTerminado = Comanda::join('facturas as f', 'f.id', 'comandas.factura_id')
            ->join('users as u', 'u.id', 'f.mozo_id')
            ->join('mesas as m', 'm.id', 'f.mesa_id')
            ->where('f.estado', 'abierta')
            ->where('comandas.estado', 'terminado')
            ->where('sectorcomanda_id', $this->sectorComanda)
            ->select('comandas.*', 'u.name', 'm.descripcion', DB::RAW("'' as demora"))
            ->orderBy('finished_at','desc')->get();
        foreach($this->infoTerminado as $i){
            $this->contadorT = $this->contadorT + 1;
            $date = Carbon::parse($i->finished_at);
            $now = Carbon::now();
            $diff = $date->diff($now);
            if ($diff->format('%h') > 0) $diff = $diff->format("%h h %i min");
            else $diff = $diff->format("%i min");
            $i->demora = $diff;
        }            
        $this->infoDetTerminado = Detcomanda::join('comandas as c', 'c.id', 'detcomandas.comanda_id')
            ->where('c.estado', 'terminado')->orderBy('descripcion')->get();

        if($this->infoEnEspera->count()){
            $this->sonido = 1;
            $this->comSelEnEspera = $this->infoEnEspera[0]->id;
        }else $this->sonido = null;
        if($this->infoProcesando->count()){ 
            $this->comSelProcesando = $this->infoProcesando[0]->id;
        }
        if($this->infoTerminado->count()){
            $this->comSelTerminado = $this->infoTerminado[0]->id;
        }
        
        return view('livewire.comandas.component');
    }

    protected $listeners = [
        'cambiarEstado'          => 'cambiarEstado',
        'seleccionarComanda'     => 'seleccionarComanda',
        'leerSinCargar'   => 'leerSinCargar'
    ];
    public function leerSinCargar()
    {
        // $this->emit('leer');
    }
    public function cambiarEstado($idComanda, $vista, $movimiento)
    {
        $mover  = 0; 
        $estado = null;
        if($idComanda != null){  
            if($vista == '1' && $movimiento == 'atras'){
                $mover = 0;
            }elseif($vista == '1' && $movimiento == 'adelante'){
                $estado = 'procesando';
                $mover  = 1; 
            }elseif($vista == '2' && $movimiento == 'atras'){
                $estado = 'en espera';
                $mover  = 1; 
            }elseif($vista == '2' && $movimiento == 'adelante'){
                $estado = 'terminado';
                $mover  = 1; 
            }elseif($vista == '3' && $movimiento == 'atras'){
                $estado = 'procesando';
                $mover  = 1; 
            }elseif($vista == '3' && $movimiento == 'adelante'){
                $mover = 0; 
            } 

            if($mover == 1){
                $record = Comanda::find($idComanda);
                // $record->update(['estado' => $estado]);
                if($vista == '2' && $movimiento == 'adelante'){
                    $record->update(['estado' => $estado, 'finished_at' => Carbon::now()]);
                }else $record->update(['estado' => $estado]);
                
                $this->infoEnEspera = Comanda::join('facturas as f', 'f.id', 'comandas.factura_id')
                    ->join('users as u', 'u.id', 'f.mozo_id')
                    ->join('mesas as m', 'm.id', 'f.mesa_id')
                    ->where('f.estado', 'abierta')
                    ->where('comandas.estado', 'en espera')
                    ->where('sectorcomanda_id', $this->sectorComanda)
                    ->select('comandas.*', 'u.name', 'm.descripcion', DB::RAW("'' as demora"))
                    ->orderBy('comandas.sent_at', 'asc')->get(); 
                foreach($this->infoEnEspera as $i){
                    $this->contadorEE = $this->contadorEE + 1;
                    $date = Carbon::parse($i->sent_at);
                    $now = Carbon::now();
                    $diff = $date->diff($now);
                    if ($diff->format('%h') > 0) $diff = $diff->format("%h h %i min");
                    else $diff = $diff->format("%i min");
                    $i->demora = $diff;
                }   
                $this->infoDetEnEspera = Detcomanda::join('comandas as c', 'c.id', 'detcomandas.comanda_id')
                    ->where('c.estado', 'en espera')->orderBy('descripcion')->get();
        
                $this->infoProcesando = Comanda::join('facturas as f', 'f.id', 'comandas.factura_id')
                    ->join('users as u', 'u.id', 'f.mozo_id')
                    ->join('mesas as m', 'm.id', 'f.mesa_id')
                    ->where('f.estado', 'abierta')
                    ->where('comandas.estado', 'procesando')
                    ->where('sectorcomanda_id', $this->sectorComanda)
                    ->select('comandas.*', 'u.name', 'm.descripcion', DB::RAW("'' as demora"))
                    ->orderBy('comandas.sent_at')->get();
                foreach($this->infoProcesando as $i){
                    $this->contadorP = $this->contadorP + 1;
                    $date = Carbon::parse($i->sent_at);
                    $now = Carbon::now();
                    $diff = $date->diff($now);
                    if ($diff->format('%h') > 0) $diff = $diff->format("%h h %i min");
                    else $diff = $diff->format("%i min");
                    $i->demora = $diff;
                }            
                $this->infoDetProcesando = Detcomanda::join('comandas as c', 'c.id', 'detcomandas.comanda_id')
                    ->where('c.estado', 'procesando')->orderBy('descripcion')->get();
        
    
                $this->infoTerminado = Comanda::join('facturas as f', 'f.id', 'comandas.factura_id')
                    ->join('users as u', 'u.id', 'f.mozo_id')
                    ->join('mesas as m', 'm.id', 'f.mesa_id')
                    ->where('f.estado', 'abierta')
                    ->where('comandas.estado', 'terminado')
                    ->where('sectorcomanda_id', $this->sectorComanda)
                    ->select('comandas.*', 'u.name', 'm.descripcion', DB::RAW("'' as demora"))
                    ->orderBy('finished_at','desc')->get();
                foreach($this->infoTerminado as $i){
                    $this->contadorT = $this->contadorT + 1;
                    $date = Carbon::parse($i->finished_at);
                    $now = Carbon::now();
                    $diff = $date->diff($now);
                    if ($diff->format('%h') > 0) $diff = $diff->format("%h h %i min");
                    else $diff = $diff->format("%i min");
                    $i->demora = $diff;
                }            
                $this->infoDetTerminado = Detcomanda::join('comandas as c', 'c.id', 'detcomandas.comanda_id')
                    ->where('c.estado', 'terminado')->orderBy('descripcion')->get();
        
    
                if($this->infoEnEspera->count()){
                    $this->comSelEnEspera = $this->infoEnEspera[0]->id;
                }
                if($this->infoProcesando->count()){ 
                    $this->comSelProcesando = $this->infoProcesando[0]->id;
                }
                if($this->infoTerminado->count()){
                    $this->comSelTerminado = $this->infoTerminado[0]->id;
                }

                $this->vista = $vista;
                $this->emit('selComanda',$this->comSelEnEspera,$this->comSelProcesando,$this->comSelTerminado);
                session()->flash('message', 'Estado registrado...');
            }else{
                $this->vista = $vista;
                $this->emit('selComanda',$this->comSelEnEspera,$this->comSelProcesando,$this->comSelTerminado);
            } 
        }
    }
    public function seleccionarComanda($idComanda, $vista, $movimiento)
    {
        if($vista == '1' && $movimiento == 'arriba'){
            $i=$this->posicion - 1;
            if($i >= 0) {
                $this->posicion = $i;
                $this->comSelEnEspera = $this->infoEnEspera[$i]->id;
            }
        }elseif($vista == '1' && $movimiento == 'abajo'){
            $i=$this->posicion + 1;
            if($i <= $this->contadorEE){
                $this->posicion = $i;
                $this->comSelEnEspera = $this->infoEnEspera[$i]->id;
            }else{
                $this->posicion = $i - 1;
                $this->comSelEnEspera = $this->infoEnEspera[$i - 1]->id;
            }               
        }

        if($vista == '2' && $movimiento == 'arriba'){
            $i=$this->posicion - 1;
            if($i >= 0) {
                $this->posicion = $i;
                $this->comSelProcesando = $this->infoProcesando[$i]->id;
            }
        }elseif($vista == '2' && $movimiento == 'abajo'){
            $i=$this->posicion + 1;
            if($i <= $this->contadorP){
                $this->posicion = $i;
                $this->comSelProcesando = $this->infoProcesando[$i]->id;
            }else{
                $this->posicion = $i - 1;
                $this->comSelProcesando = $this->infoProcesando[$i - 1]->id;
            }              
        }

        if($vista == '3' && $movimiento == 'arriba'){
            $i=$this->posicion - 1;
            if($i >= 0) {
                $this->posicion = $i;
                $this->comSelTerminado = $this->infoTerminado[$i]->id;
            }
        }elseif($vista == '3' && $movimiento == 'abajo'){
            $i=$this->posicion + 1;
            if($i <= $this->contadorT){
                $this->posicion = $i;
                $this->comSelTerminado = $this->infoTerminado[$i]->id;
            }else{
                $this->posicion = $i - 1;
                $this->comSelTerminado = $this->infoTerminado[$i - 1]->id;
            }                
        }
        $this->vista = $vista;
        $this->emit('selComanda',$this->comSelEnEspera,$this->comSelProcesando,$this->comSelTerminado);
    }
}
