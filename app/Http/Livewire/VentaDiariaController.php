<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Factura;
use Carbon\Carbon;
use DB;

class VentaDiariaController extends Component
{
    public $pagination = 5, $comercioId, $estado = 1;

    public function render()
    {
         //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');

        switch ($this->estado) {
            case '1': //todas
                $info = Factura::where('facturas.comercio_id', $this->comercioId)
                            ->whereDate('facturas.created_at', Carbon::today())
                            ->where('facturas.estado', 'contado')
                            ->orWhere('facturas.comercio_id', $this->comercioId)
                            ->whereDate('facturas.created_at', Carbon::today())
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
                        $i->nomCli = '';
                    }    
                    if($i->repartidor_id != null){
                        $infoRep = Factura::join('users as u', 'u.id', 'facturas.repartidor_id')
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
                $info = Factura::where('facturas.comercio_id', $this->comercioId)
                    ->whereDate('facturas.created_at', Carbon::today())
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
                        $i->nomCli = '';
                    }    
                    if($i->repartidor_id != null){
                        $infoRep = Factura::join('users as u', 'u.id', 'facturas.repartidor_id')
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
                $info = Factura::where('facturas.comercio_id', $this->comercioId)
                    ->whereDate('facturas.created_at', Carbon::today())
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
                        $i->nomCli = '';
                    }    
                    if($i->repartidor_id != null){
                        $infoRep = Factura::join('users as u', 'u.id', 'facturas.repartidor_id')
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

        $total = 0;
        foreach($info as $i){
            $total += $i->importe;
        }

        return view('livewire.reportes.component-ventas-diarias',
            [   'info'     => $info,
                'sumaTotal'=> $total
            ]);
    }
}
