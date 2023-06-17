<?php

namespace App\Http\Livewire;

use Livewire\WithPagination;
use Livewire\Component;
use App\Models\ArqueoGral;
use App\Models\Cliente;
use App\Models\Comercio;
use App\Models\Detfactura;
use App\Models\Factura;
use App\Models\Producto;
use App\Models\User;
use Carbon\Carbon;
use DB;

class VentasPorFechasController extends Component
{
    public $search, $fecha_ini, $fecha_fin, $cantVentas, $verFacturas = 0;
    public $comercioId, $arqueoGralId, $nro_arqueo = null, $fecha_arqueo = null, $hora_arqueo = null;

    public function render()
    {
        //busca el comercio que está en sesión y el id del ArqueoGral
        $this->comercioId = session('idComercio');
        $this->arqueoGralId = session('idArqueoGral');
        session(['facturaPendiente' => null]);  

        if($this->fecha_ini) $fi = Carbon::parse($this->fecha_ini)->format('Y-m-d'). ' 00:00:00';
        if($this->fecha_fin) $ff = Carbon::parse($this->fecha_fin)->format('Y-m-d'). ' 23:59:00';
        $infoProd = [];
        $ventas = [];
        $total = 0;
        if(!$this->search){
            if($this->fecha_ini && $this->fecha_fin){
                //averiguamos la hora de apertura del comercio para comprobar el arqueo
                $horaApertura = Comercio::select('hora_apertura')
                    ->where('id', $this->comercioId)->first();
                    
                $arqueo = ArqueoGral::where('created_at', '>', $fi)
                    ->where('comercio_id', $this->comercioId)
                    ->select('id', 'created_at')
                    ->selectRaw('DATE(created_at) as fecha')
                    ->selectRaw('TIME(created_at) as hora')
                    ->orderBy('created_at')->first();
                $f_inicio = Carbon::parse($this->fecha_ini)->format('Y-m-d');

                if($arqueo){
                    if($f_inicio == $arqueo->fecha){
                        if($arqueo->hora > $horaApertura->hora_apertura){
                            $this->nro_arqueo = $arqueo->id;
                            $this->fecha_arqueo =  $arqueo->fecha; 
                            $this->hora_arqueo =  $arqueo->hora; 
                        }else $this->emit('arqueo');
                    }else $this->emit('arqueo');
                }else $this->emit('arqueo'); 
            }
            $ventas = Factura::join('caja_usuarios as cu', 'cu.id', 'facturas.arqueo_id')
                ->where('cu.arqueo_gral_id', $this->nro_arqueo)
                ->select('facturas.*', DB::RAW("'' as cliente"), DB::RAW("'' as repartidor"))
                ->orderBy('id', 'desc')->get();
            
            foreach ($ventas as $i){
                $total = $total + $i->importe;
                if($i->cliente_id != null){
                    $infoCli = Cliente::find($i->cliente_id);
                    $i->cliente = $infoCli->apellido . ' ' . $infoCli->nombre;
                }else {
                    $i->cliente = 'C/F';
                }    
                if($i->user_id != null){
                    $infoRep = User::find($i->user_id); 
                    $i->repartidor = $infoRep->apellido . ' ' . $infoRep->name;                        
                }else {
                    $i->repartidor = ''; 
                }
            } 
        }else{
       // $total = Factura::whereBetween('created_at', [$fi, $ff])->where('estado', 'PAGADA')->sum('importe');


           $infoProd = Detfactura::join('productos as p', 'p.id', 'detfacturas.producto_id')
                ->where('detfacturas.comercio_id', $this->comercioId) 
                ->where('p.descripcion', 'like', '%' .  $this->search . '%')
                ->whereBetween('detfacturas.created_at', [$fi, $ff])
                ->groupBy('p.descripcion')
                ->select('p.descripcion', DB::RAW("0 as cantidad"))->get();
            if($infoProd){
                foreach ($infoProd as $i) {
                    $cantidad = Detfactura::join('productos as p', 'p.id', 'detfacturas.producto_id')
                        ->where('detfacturas.comercio_id', $this->comercioId) 
                        ->where('p.descripcion', $i->descripcion)
                        ->whereBetween('detfacturas.created_at', [$fi, $ff])
                        ->sum('detfacturas.cantidad');
                    $i->cantidad = $cantidad;
                }
            }  
       }
       
       
       
       return view('livewire.reportes.component-ventas-por-fechas', [
            'info'      => $ventas,
            'sumaTotal' => $total,
            'infoProd'  => $infoProd
        ]);
    }
    public function ver_facturas()
    {
        if($this->fecha_ini !=''){
            if($this->verFacturas == 0) $this->verFacturas = 1;
            else $this->verFacturas = 0;
        }
    }
    public function hoy()
    {
        $this->fecha_ini = Carbon::now();
        $this->fecha_fin = Carbon::now();
    }
          //     $ventas = Factura::where('deleted_at', null)
        //         ->where('comercio_id', $this->comercioId)
        //         ->whereBetween('created_at', [$fi, $ff])
        //         ->sum('importe');
      
}