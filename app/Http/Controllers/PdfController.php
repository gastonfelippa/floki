<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// use Livewire\Component;
use App\Models\Factura;
use App\Models\Cliente;
use App\Models\Ctacte;
use App\Models\Recibo;
use App\Models\Producto;
use App\Models\Detfactura;
use App\Models\ReciboFactura;
use App\Models\Vianda;
use Carbon\Carbon;
use PDF;
use DB;

class PdfController extends Controller
{
    public $comercioId, $entrega = 0, $suma;
    
    public function PDF() {
        $pdf = PDF::loadView('prueba');
        return $pdf->stream('prueba.pdf');
    }

    public function PDFFacturas() {

        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio'); 

        $clientes  = Cliente::all();
   
        $info = Factura::leftjoin('clientes as c','c.id','facturas.cliente_id')
            ->leftjoin('empleados as r','r.id','facturas.repartidor_id')
            ->select('facturas.*', 'c.nombre as nomCli', 'c.apellido as apeCli',
                     'r.nombre as nomRep', 'r.apellido as apeRep',DB::RAW("'' as total"))
            ->where('facturas.estado','CTACTE')
            ->where('facturas.comercio_id', $this->comercioId)
            ->orderBy('facturas.id', 'asc')->get(); 

        $pdf = PDF::loadView('livewire.pdf.pdfFacturas', compact('info'));
        return $pdf->stream('facturas.pdf');
    }

    public function PDFRecibos($id) {
        $info = ReciboFactura::leftjoin('facturas as f','f.id','recibo_facturas.factura_id')
            ->leftjoin('recibos as r','r.id','recibo_facturas.recibo_id')
            ->leftjoin('clientes as c','c.id','f.cliente_id')
            ->leftjoin('localidades as l','l.id','c.localidad_id')
            ->where('recibo_facturas.recibo_id', $id)
            ->select('r.numero as nro_recibo', 'r.importe as total', 'r.entrega', 
                     'recibo_facturas.created_at as fecha_recibo', 'f.created_at as fecha', 
                     'f.numero as num_factura', 'f.importe', 'c.nombre', 'c.apellido',
                     'c.calle', 'c.numero', 'l.descripcion')
            ->orderBy('f.created_at', 'asc')->get(); 
        foreach($info as $i){
            $info->fecha = $i->fecha;            
            $info->num_factura =  $i->num_factura;          
            $info->importe =  $i->importe;  
        }
        $pdf = PDF::loadView('livewire.pdf.pdfRecibos', compact('info'));
        return $pdf->stream('facturas.pdf');
    }

    public function PDFFactDel($id) {
        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio'); 

        $clientes  = Cliente::select()->where('comercio_id', $this->comercioId)->get();
        $productos = Producto::select()->where('comercio_id', $this->comercioId)->get();
      
        $infoDetalle = Detfactura::leftjoin('facturas as f','f.id','detfacturas.factura_id')
          ->leftjoin('productos as p','p.id','detfacturas.producto_id')
          ->select('detfacturas.*', 'p.descripcion as producto', DB::RAW("'' as importe"))
          ->where('detfacturas.factura_id', $id)
          ->where('detfacturas.comercio_id', $this->comercioId)
          ->orderBy('detfacturas.id', 'asc')->get(); 

      $this->importeFactura = 0;  

      foreach ($infoDetalle as $i)
      {
          $i->importe=$i->cantidad * $i->precio;
          $this->importeFactura += $i->importe;
      }
        $info = Factura::leftjoin('clientes as c','c.id','facturas.cliente_id')
            ->leftjoin('empleados as r','r.id','facturas.repartidor_id')
            ->select('facturas.*', 'facturas.id as id', 'c.nombre as nomCli', 'c.apellido as apeCli', 
                     'c.calle as calleCli', 'c.numero as numCli')
            ->where('facturas.id','like',$id)->get();

        if($info[0]->nomCli == null) {
            $delivery = false;
        }else {              
            $delivery = true;
        }
        $pdf = PDF::loadView('livewire.pdf.pdfFactDel', compact(['infoDetalle','info','delivery']));
        return $pdf->stream('fact.pdf');
    }

    public function PDFViandas() {

        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio'); 

        $dt = Carbon::now();
        $diaDeLaSemana=date('w');
        
        switch ($diaDeLaSemana) {
            case '1':
                $info = Vianda::join('clientes as c', 'c.id', 'viandas.cliente_id')
                        ->where('viandas.c_lunes_m', '<>', '')
                        ->where('c.comercio_id', $this->comercioId)
                        ->select('viandas.c_lunes_m as cantidad','viandas.h_lunes_m as hora',
                                 'c.apellido', 'c.nombre')->orderBy('viandas.h_lunes_m')->get();                
            break;
        case '2':
                $info = Vianda::join('clientes as c', 'c.id', 'viandas.cliente_id')
                        ->where('viandas.c_martes_m', '<>', '')
                        ->where('c.comercio_id', $this->comercioId)
                        ->select('viandas.c_martes_m as cantidad','viandas.h_martes_m as hora',
                                 'c.apellido', 'c.nombre')->get(); 
            break;
        case '3':
                $info = Vianda::join('clientes as c', 'c.id', 'viandas.cliente_id')
                        ->where('viandas.c_miercoles_m', '<>', '')
                        ->where('c.comercio_id', $this->comercioId)
                        ->select('viandas.c_miercoles_m as cantidad','viandas.h_miercoles_m as hora',
                                 'c.apellido', 'c.nombre')->get(); 
            break;
        case '4':
                $info = Vianda::join('clientes as c', 'c.id', 'viandas.cliente_id')
                        ->where('viandas.c_jueves_m', '<>', '')
                        ->where('c.comercio_id', $this->comercioId)
                        ->select('viandas.c_jueves_m as cantidad','viandas.h_jueves_m as hora',
                                 'c.apellido', 'c.nombre')->orderBy('h_jueves_m')->get(); 
            break;
        case '5':
                $info = Vianda::join('clientes as c', 'c.id', 'viandas.cliente_id')
                        ->where('viandas.c_viernes_m', '<>', '')
                        ->where('c.comercio_id', $this->comercioId)
                        ->select('viandas.c_viernes_m as cantidad','viandas.h_viernes_m as hora', 
                                 'c.apellido', 'c.nombre')->get(); 
            break;
        case '6':
                $info = Vianda::join('clientes as c', 'c.id', 'viandas.cliente_id')
                        ->where('c.vianda', '1')
                        ->where('viandas.c_sabado_m', '<>', '')
                        ->where('c.comercio_id', $this->comercioId)
                        ->select('c_sabado_m as cantidad','h_sabado_m as hora','c.apellido', 'c.nombre')
                        ->orderBy('h_sabado_m')->get();       
            break;
        case '7':
                $info = Vianda::join('clientes as c', 'c.id', 'viandas.cliente_id')
                        ->where('viandas.c_domingo_m', '<>', '')
                        ->where('c.comercio_id', $this->comercioId)
                        ->select('viandas.c_domingo_m as cantidad','viandas.h_domingo_m as hora',
                                 'c.apellido', 'c.nombre')->get(); 
            break;
            default:
        }
        $pdf = PDF::loadView('livewire.pdf.pdfViandas', compact('info'));
        return $pdf->stream('viandas.pdf');
    }

    public function PDFListadoCtaCte()
    {
        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');

        $info = Ctacte::join('clientes as c', 'c.id', 'cta_cte.cliente_id')
            ->where('c.comercio_id', $this->comercioId)
            ->where('c.saldo', '1')
            ->select('cta_cte.cliente_id', 'c.nombre', 'c.apellido', DB::RAW("'' as importe"))
            ->groupBy('cta_cte.cliente_id', 'c.nombre', 'c.apellido')
            ->orderBy('c.apellido')->orderBy('c.nombre')->get();
        $suma=0;
        foreach($info as $i) {
            $sumaFacturas=0;
            $sumaRecibos=0;
            //verifico si el registro es una factura o es un recibo
            $registroCtaCte = Ctacte::where('cta_cte.cliente_id', $i->cliente_id)->get();
            foreach($registroCtaCte as $r) {   
                if($r->factura_id != null) {    //si es factura las voy sumando
                    $importe = Factura::where('id', $r->factura_id)
                        ->where('estado', 'ctacte')
                        ->where('estado_pago', '0')
                        ->orWhere('id', $r->factura_id)
                        ->where('estado', 'ctacte')
                        ->where('estado_pago', '2')
                        ->select('importe')->get();
                    foreach($importe as $imp){
                           $sumaFacturas += $imp->importe; //calculo el total de las facturas de cada cliente
                    }
                }else { 
                    if($sumaFacturas != 0){
                        $importe = Recibo::where('id', $r->recibo_id)   //busco todos los recibos
                            ->where('entrega', 1)
                            ->select('importe')->get();
                        foreach($importe as $imp){
                            $sumaRecibos += $imp->importe; //calculo el total de recibos de cada cliente
                        }
                    }                        
                }
            }
            // calculo el total para cada cliente
            $i->importe = $sumaFacturas - $sumaRecibos;
            // solo calculo el importe del total gral si se están mostrando todos los clientes
            $suma += $i->importe;
        }  
        $pdf = PDF::loadView('livewire.pdf.pdfListadoCtaCte', compact('info','suma'));
        return $pdf->stream();  
    }
    public function PDFResumenDeCuenta($cliId)
    {
        $importeEntrega =0;
        $info = Ctacte::join('clientes as c', 'c.id', 'cta_cte.cliente_id')
            ->join('facturas as f', 'f.id', 'cta_cte.factura_id')
            ->where('cta_cte.cliente_id', $cliId)
            ->where('f.estado', 'ctacte')
            ->where('f.estado_pago', '0')
            ->orWhere('cta_cte.cliente_id', $cliId)
            ->where('f.estado', 'ctacte')
            ->where('f.estado_pago', '2')
            ->select('cta_cte.factura_id', 'cta_cte.recibo_id', 'cta_cte.cliente_id', 
                    'c.nombre', 'c.apellido', DB::RAW("'' as fecha"), DB::RAW("'' as numero") , DB::RAW("'' as importe"), 
                    DB::RAW("'' as importe_factura"), DB::RAW("'' as resto"))
            ->orderBy('cta_cte.created_at')->get();

        $infoEntrega = Ctacte::join('recibos as r', 'r.id', 'cta_cte.recibo_id')
            ->where('cta_cte.cliente_id', $cliId)
            ->where('r.entrega', '1')
            ->select('r.id','r.importe')->get();
        foreach($infoEntrega as $i){
            $verEstadoPagoFactura = ReciboFactura::join('facturas as f','f.id','recibo_facturas.factura_id')
                ->where('recibo_facturas.recibo_id',$i->id)
                ->where('f.estado_pago','2')->get();
            if($verEstadoPagoFactura->count() > 0)
            $importeEntrega += $i->importe; 
        }  

        $sumaFacturas=0;
        $sumaRecibos=0;
        foreach($info as $i) {
            //busco todas las facturas
            $importe = Ctacte::join('facturas as f', 'f.id', 'cta_cte.factura_id') 
                ->where('f.id', $i->factura_id)
                ->where('f.estado', 'ctacte')
                ->where('f.estado_pago', '0')
                ->orWhere('f.id', $i->factura_id)
                ->where('f.estado', 'ctacte')
                ->where('f.estado_pago', '2')
                ->select('f.estado_pago', 'f.importe as importe', 'f.numero', 'f.created_at')->get();
            $sumaFacturas += $importe[0]->importe; //calculo el total de las facturas de cada cliente
            if($importe[0]->estado_pago == 0) $i->importe_factura = 1; //aviso de factura para pintar rojo   
            else $i->importe_factura = 2;  //aviso de factura para pintar rojo/negrita            
        
            $i->fecha = $importe[0]->created_at;                        
            $i->numero = $importe[0]->numero;                        
            $i->importe = $importe[0]->importe;
            //recupero las entregas y calculo el resto las facturas que correspondan
            if($importe[0]->estado_pago == 2){
                $entregas = 0;
                $pagos = ReciboFactura::join('recibos as r', 'r.id', 'recibo_facturas.recibo_id')
                    ->where('recibo_facturas.factura_id', $i->factura_id)
                    ->select('r.importe')->get();
                foreach($pagos as $p){
                    $entregas += $p->importe;
                }
                $i->resto = $i->importe - $entregas;
            }
        } 
        $totalCli = $sumaFacturas;
        //calculo el saldo del cliente seleccionado
        $saldo = $totalCli - $importeEntrega;
        $pdf = PDF::loadView('livewire.pdf.pdfResumenDeCuenta', compact('info','importeEntrega','totalCli','saldo'));
        return $pdf->stream();
    }
}
