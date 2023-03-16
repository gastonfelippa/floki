<?php

namespace App\Http\Livewire;

use Livewire\WithPagination;
use Livewire\Component;
use App\Models\ArqueoGral;
use App\Models\Comercio;
use App\Models\Factura;
use Carbon\Carbon;
use DB;

class VentasPorFechasController extends Component
{
    public $fecha_ini = '', $fecha_fin, $cantVentas, $verFacturas = 0;
    public $comercioId, $arqueoGralId, $nro_arqueo = null, $fecha_arqueo = null, $hora_arqueo = null;

    public function render()
    {
        //busca el comercio que está en sesión y el id del ArqueoGral
        $this->comercioId = session('idComercio');
        $this->arqueoGralId = session('idArqueoGral');
        session(['facturaPendiente' => null]);  

      //  $fi = Carbon::parse(Carbon::now())->format('Y-m-d'). ' 00:00:00';
      //  $ff = Carbon::parse(Carbon::now())->format('Y-m-d'). ' 23:59:00';

        if($this->fecha_ini !=''){
            // $fi = Carbon::parse($this->fecha_ini)->format('Y-m-d'). ' 00:00:00';
            // $ff = Carbon::parse($this->fecha_fin)->format('Y-m-d'). ' 23:59:00';

            //averiguamos la hora de apertura del comercio para comprobar el arqueo
            $horaApertura = Comercio::select('hora_apertura')
                ->where('id', $this->comercioId)->first();
                
            $fi = Carbon::parse($this->fecha_ini)->format('Y-m-d');
            $arqueo = ArqueoGral::where('created_at', '>', $fi)->select('id', 'created_at')->orderBy('created_at')->first();
 
            $fecha_arqueo = DB::table('arqueo_grals')
                ->where('created_at', '>', $fi)
                ->selectRaw('DATE(created_at) as fecha')
                ->first();
            $hora_arqueo = DB::table('arqueo_grals')
                ->where('created_at', '>', $fi)
                ->selectRaw('TIME(created_at) as hora')
                ->first();
            if($fi == $fecha_arqueo->fecha){
                if($hora_arqueo->hora > $horaApertura->hora_apertura){
                    $this->nro_arqueo = $arqueo->id;
                    $this->fecha_arqueo =  $fecha_arqueo->fecha; 
                    $this->hora_arqueo =  $hora_arqueo->hora; 
                } 
            }
        }

        $ventas = Factura::join('clientes as c', 'c.id', 'facturas.cliente_id')
            ->join('users as u', 'u.id', 'facturas.repartidor_id')
            ->join('caja_usuarios as cu', 'cu.id', 'facturas.arqueo_id')
            ->where('cu.arqueo_gral_id', $this->nro_arqueo)
            ->select('facturas.*', 'c.nombre as cliente', 'u.name as repartidor')
            ->orderBy('id', 'desc')->get();
        $totalVentas = Factura::join('caja_usuarios as cu', 'cu.id', 'facturas.arqueo_id')
            ->where('cu.arqueo_gral_id', $this->nro_arqueo)
            ->select('facturas.*')->get();
        $this->cantVentas = $totalVentas->count();
        $total = 0;
        foreach($totalVentas as $i){
            $total = $total + $i->importe;
        }

       // $total = Factura::whereBetween('created_at', [$fi, $ff])->where('estado', 'PAGADA')->sum('importe');

        return view('livewire.reportes.component-ventas-por-fechas', [
            'info'      => $ventas,
            'sumaTotal' => $total
        ]);
    }
    public function ver_facturas()
    {
        if($this->fecha_ini !=''){
            if($this->verFacturas == 0) $this->verFacturas = 1;
            else $this->verFacturas = 0;
        }
    }
}