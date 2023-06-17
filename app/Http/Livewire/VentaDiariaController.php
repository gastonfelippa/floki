<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Factura;
use DB;

class VentaDiariaController extends Component
{
    public $comercioId, $arqueoGralId, $estado = 1, $search = null, $searchCli = null;

    public function render()
    {
         //busca el comercio que está en sesión y el id del ArqueoGral
        $this->comercioId = session('idComercio');
        $this->arqueoGralId = session('idArqueoGral');  

        if(strlen($this->search) > 0){
            $info = Factura::join('caja_usuarios as cu', 'cu.id', 'facturas.arqueo_id')
                ->join('users as u', 'u.id', 'facturas.user_id')
                ->where('facturas.comercio_id', $this->comercioId)
                ->where('cu.arqueo_gral_id', $this->arqueoGralId)
                ->where('facturas.numero', $this->search)
                ->orderBy('facturas.id')
                ->select('facturas.*','u.name as nomRep','u.apellido as apeRep',
                        DB::RAW("'' as nomCli"),DB::RAW("'' as apeCli"))
                ->get();
              
            foreach ($info as $i){
                if($i->cliente_id != null) $i->nomCli = $i->apeCli . ' ' . $i->nomCli; 
                else $i->nomCli = 'C/F'; 
                if($i->user_id != null) $i->nomRep = $i->apeRep . ' ' . $i->nomRep;
                else $i->nomRep = ''; 
            }
        }elseif (strlen($this->searchCli) > 0) {
            $info = Factura::join('clientes as c', 'c.id', 'facturas.cliente_id')
                ->join('caja_usuarios as cu', 'cu.id', 'facturas.arqueo_id')
                ->join('users as u', 'u.id', 'facturas.user_id')
                ->where('facturas.comercio_id', $this->comercioId)
                ->where('cu.arqueo_gral_id', $this->arqueoGralId)
                ->where('c.apellido', 'like', '%' .  $this->searchCli . '%')
                ->orWhere('facturas.comercio_id', $this->comercioId)
                ->where('cu.arqueo_gral_id', $this->arqueoGralId)
                ->where('c.nombre', 'like', '%' .  $this->searchCli . '%')
                ->orderBy('facturas.id')
                ->select('facturas.*','u.name as nomRep','u.apellido as apeRep',
                        'c.nombre as nomCli','c.apellido as apeCli')
                ->get();
            foreach ($info as $i){
                if($i->cliente_id != null) $i->nomCli = $i->apeCli . ' ' . $i->nomCli; 
                else $i->nomCli = 'C/F'; 
                if($i->user_id != null) $i->nomRep = $i->apeRep . ' ' . $i->nomRep;
                else $i->nomRep = ''; 
            }
        }else{
            switch ($this->estado) {
                case '1': //todas
                    $info = Factura::join('caja_usuarios as cu', 'cu.id', 'facturas.arqueo_id')
                        ->where('cu.arqueo_gral_id', $this->arqueoGralId)
                        ->where('facturas.estado', 'contado')
                        ->orWhere('cu.arqueo_gral_id', $this->arqueoGralId)
                        ->where('facturas.estado', 'ctacte')
                        ->orderBy('facturas.id', 'desc')
                        ->select('facturas.*',DB::RAW("'' as nomCli"),DB::RAW("'' as nomRep"))
                        ->get();
                    foreach ($info as $i){
                        if($i->cliente_id != null){
                            $infoCli = Factura::join('clientes as c', 'c.id', 'facturas.cliente_id')
                                ->where('facturas.id', $i->id)
                                ->select('c.nombre as nomCli', 'c.apellido as apeCli')
                                ->get();
                            $i->nomCli = $infoCli[0]->apeCli . ' ' . $infoCli[0]->nomCli;
                        }else {
                            $i->nomCli = 'C/F';
                        }    
                        if($i->user_id != null){
                            $infoRep = Factura::join('users as u', 'u.id', 'facturas.user_id')
                                ->where('facturas.id', $i->id)
                                ->select('u.name as nomRep', 'u.apellido as apeRep')
                                ->get();    
                            $i->nomRep = $infoRep[0]->apeRep . ' ' . $infoRep[0]->nomRep;                        
                        }else {
                            $i->nomRep = ''; 
                        }
                    }
                    break;
                case '2': //contado  
                    $info = Factura::join('caja_usuarios as cu', 'cu.id', 'facturas.arqueo_id')
                        ->where('cu.arqueo_gral_id', $this->arqueoGralId)
                        ->where('facturas.estado', 'contado')
                        ->orderBy('facturas.id', 'desc')
                        ->select('facturas.*',DB::RAW("'' as nomCli"),DB::RAW("'' as nomRep"))
                        ->get();
                    foreach ($info as $i){
                        if($i->cliente_id != null){
                            $infoCli = Factura::join('clientes as c', 'c.id', 'facturas.cliente_id')
                                ->where('facturas.id', $i->id)
                                ->select('c.nombre as nomCli', 'c.apellido as apeCli')
                                ->get();
                            $i->nomCli = $infoCli[0]->apeCli . ' ' . $infoCli[0]->nomCli;
                        }else {
                            $i->nomCli = 'C/F';
                        }    
                        if($i->user_id != null){
                            $infoRep = Factura::join('users as u', 'u.id', 'facturas.user_id')
                                ->where('facturas.id', $i->id)
                                ->select('u.name as nomRep', 'u.apellido as apeRep')
                                ->get();    
                            $i->nomRep = $infoRep[0]->apeRep . ' ' . $infoRep[0]->nomRep;                        
                        }else {
                            $i->nomRep = ''; 
                        }
                    }
                    break;
                case '3': //ctacte 
                    $info = Factura::join('caja_usuarios as cu', 'cu.id', 'facturas.arqueo_id')
                        ->where('cu.arqueo_gral_id', $this->arqueoGralId)
                        ->where('facturas.estado', 'ctacte')
                        ->orderBy('facturas.id', 'desc')
                        ->select('facturas.*',DB::RAW("'' as nomCli"),DB::RAW("'' as nomRep"))
                        ->get();
                    foreach ($info as $i){
                        if($i->cliente_id != null){
                            $infoCli = Factura::join('clientes as c', 'c.id', 'facturas.cliente_id')
                                ->where('facturas.id', $i->id)
                                ->select('c.nombre as nomCli', 'c.apellido as apeCli')
                                ->get();
                            $i->nomCli = $infoCli[0]->apeCli . ' ' . $infoCli[0]->nomCli;
                        }else {
                            $i->nomCli = 'C/F';
                        }    
                        if($i->user_id != null){
                            $infoRep = Factura::join('users as u', 'u.id', 'facturas.user_id')
                                ->where('facturas.id', $i->id)
                                ->select('u.name as nomRep', 'u.apellido as apeRep')
                                ->get();    
                            $i->nomRep = $infoRep[0]->apeRep . ' ' . $infoRep[0]->nomRep;                        
                        }else {
                            $i->nomRep = ''; 
                        }
                    }
                    break;
                default:
            }
        }
        $total = 0;
        if(!$this->search && !$this->searchCli){
            foreach($info as $i){
                $total += $i->importe;
            }
        }

        return view('livewire.reportes.component-ventas-diarias',
            [   'info'     => $info,
                'sumaTotal'=> $total
            ]);
    }

    protected $listeners = ['limpiarSearch' => 'limpiarSearch'];

    public function limpiarSearch()
    {
        $this->search = null;
        $this->searchCli = null;
    }
}
