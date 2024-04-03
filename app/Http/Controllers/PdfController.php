<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// use Livewire\Component;
use App\Models\Banco;
use App\Models\Cheque;
use App\Models\Cliente;
use App\Models\Comercio;
use App\Models\Ctacte;
use App\Models\CtaCteClub;
use App\Models\Debito;
use App\Models\DebitoGenerado;
use App\Models\Detfactura;
use App\Models\DetMetodoPago;
use App\Models\Detpedido;
use App\Models\DetRemito;
use App\Models\Factura;
use App\Models\Localidad;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Subproducto;
use App\Models\Recibo;
use App\Models\ReciboFactura;
use App\Models\Remito;
use App\Models\Vianda;
use App\Models\User;
use Carbon\Carbon;
use PDF;
use DB;

class PdfController extends Controller
{
    public $comercioId, $modComandas, $entrega = 0, $suma;

    public function PDFCuotaSocio()
    {
        $ultimo_debito_generado = DebitoGenerado::select('mes_año')->orderBy('id', 'desc')->first();
 
        $debitos = Debito::join('socios as s', 's.id', 'debitos.socio_id')
            ->join('det_debitos as dd', 'dd.debito_id', 'debitos.id')
            ->join('debitos_generados as dg', 'dg.id', 'dd.debito_generado_id')
            ->where('dg.mes_año', $ultimo_debito_generado->mes_año)
            ->select('s.apellido', 's.nombre', 'debitos.numero', 'debitos.importe', 'dg.mes_año', DB::RAW("'' as nomApe"))->orderBy('s.apellido')->get();
        foreach($debitos as $d){
            $d->nomApe = substr($d->apellido . ' ' . $d->nombre,0,22);
        }
        $pdf = PDF::loadView('livewire.pdf.pdfCuotaSocio', compact('debitos'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('cuotasocio.pdf');
        // return $pdf->download('cuotasocio.pdf');
    }    
    public function PDFResumenDeCuentaClub($socioId)
    {
        $importeEntrega =0;
        $info = CtaCteClub::join('socios as s', 's.id', 'cta_cte_club.socio_id')
            ->join('facturas as f', 'f.id', 'cta_cte_club.factura_id')
            ->where('cta_cte_club.socio_id', $cliId)
            ->where('f.estado', 'ctacte')
            ->where('f.estado_pago', '0')
            ->orWhere('cta_cte_club.socio_id', $cliId)
            ->where('f.estado', 'ctacte')
            ->where('f.estado_pago', '2')
            ->select('cta_cte_club.factura_id', 'cta_cte_club.recibo_id', 'cta_cte_club.socio_id', 
                    's.nombre', 's.apellido', DB::RAW("'' as fecha"), DB::RAW("'' as numero") , DB::RAW("'' as importe"), 
                    DB::RAW("'' as importe_factura"), DB::RAW("'' as resto"))
            ->orderBy('cta_cte_club.created_at')->get();

        $infoEntrega = CtaCteClub::join('recibos as r', 'r.id', 'cta_cte.recibo_id')
            ->where('cta_cte.socio_id', $cliId)
            ->where('r.entrega', '1')
            ->select('r.id','r.importe')->get();
        foreach($infoEntrega as $i){
            $verEstadoPagoFactura = ReciboFactura::join('facturas as f','f.id','recibo_facturas.factura_id')
                ->where('recibo_facturas.recibo_id',$i->id)
                ->where('f.estado_pago','2')->get();
            if($verEstadoPagoFactura->count() > 0)
            $importeEntrega += $i->importe; 
        }  

        $sumaDebitos=0;
        $sumaRecibos=0;
        foreach($info as $i) {
            //busco todas las facturas
            $importe = CtaCteClub::join('facturas as f', 'f.id', 'cta_cte.factura_id') 
                ->where('f.id', $i->factura_id)
                ->where('f.estado', 'ctacte')
                ->where('f.estado_pago', '0')
                ->orWhere('f.id', $i->factura_id)
                ->where('f.estado', 'ctacte')
                ->where('f.estado_pago', '2')
                ->select('f.estado_pago', 'f.importe as importe', 'f.numero', 'f.created_at')->get();
            $sumaDebitos += $importe[0]->importe; //calculo el total de las facturas de cada cliente
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
        $totalCli = $sumaDebitos;
        //calculo el saldo del cliente seleccionado
        $saldo = $totalCli - $importeEntrega;
        $pdf = PDF::loadView('livewire.pdf.pdfResumenDeCuenta', compact('info','importeEntrega','totalCli','saldo'));
        return $pdf->stream();
    }    
    public function PDF() 
    {
        $pdf = PDF::loadView('prueba');
        return $pdf->stream('prueba.pdf');
    }
    public function PDFFacturas() 
    {
        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio'); 
   
        $info = Factura::leftjoin('clientes as c','c.id','facturas.cliente_id')
            ->leftjoin('empleados as r','r.id','facturas.repartidor_id')
            ->select('facturas.*', 'c.nombre as nomCli', 'c.apellido as apeCli',
                     'r.nombre as nomRep', 'r.apellido as apeRep', DB::RAW("'' as total"))
            ->where('facturas.estado','CTACTE')
            ->where('facturas.comercio_id', $this->comercioId)
            ->orderBy('facturas.id', 'asc')->get(); 

        $pdf = PDF::loadView('livewire.pdf.pdfFacturas', compact('info'));
        return $pdf->stream('facturas.pdf');
    }
    public function PDFRecibos($id) 
    {
        $fecha_recibo = Recibo::find($id);
        //$fecha_recibo = $fecha_recibo->created_at;
        //dd($fecha_recibo);
        //busco todas las facturas que fueron pagadas por este recibo que tiene el id = $id
        $info_factura = ReciboFactura::join('facturas as f','f.id','recibo_facturas.factura_id')
            ->join('recibos as r','r.id', 'recibo_facturas.recibo_id')
            ->join('det_metodo_pagos as det','det.recibo_id', 'recibo_facturas.recibo_id')
            ->join('clientes as c','c.id','f.cliente_id')
            ->join('localidades as l','l.id','c.localidad_id')
            ->where('recibo_facturas.recibo_id', $id)
            ->select('r.numero as nro_recibo', 'r.entrega', 'r.created_at as fecha_recibo', 
                    'f.id as factura_id', 'f.created_at as fecha_fac', 'f.numero as num_factura', 
                    'f.importe as importe_fac', 'c.nombre', 'c.apellido', 'c.calle', 'c.numero', 
                    'l.descripcion', 'det.importe')->get();                    
        $total = 0;
        foreach($info_factura as $i){
            $total += $i->importe_fac;     //calculo el total de comprobantes cancelados
        }
        $total_comprobantes = $total;
        
        //capturo el id de la factura que cancela este recibo para luego ver si tiene algún pago a cuenta
        $factura_id = $info_factura[0]->factura_id;

        //busco los datos de pago que corresponden a este/estos recibo/s
        $info_recibo = ReciboFactura::where('factura_id', $factura_id)->select('recibo_id', DB::RAW("'' as fecha_rec"), 
            DB::RAW("'' as medio_de_pago"), DB::RAW("0 as importe_rec"))->get();
        //si hay más de un pago a cuenta, muestro el total de pagos a cuenta
        $mas_de_un_pago_a_cuenta = 0;    
        if($info_recibo->count() > 1) $mas_de_un_pago_a_cuenta = 1;

        $total_pagos = 0;
        foreach($info_recibo as $i){
            $recibo = DetMetodoPago::where('recibo_id', $i->recibo_id)
                ->select('created_at', 'medio_de_pago', 'num_comp_pago', 'importe')->get();
            $i->fecha_rec       = $recibo[0]->created_at;             
            $i->importe_rec     = $recibo[0]->importe; 
          
            $total_pagos += $i->importe_rec;      //calculo el total de pagos a cuenta
            //creo el detalle del medio de pago
            switch ($recibo[0]->medio_de_pago) {
                case '1':
                    $i->medio_de_pago = 'Efectivo';
                    break;
                case '2':
                    $i->medio_de_pago = 'Tarj. de Débito - Comprobante N°: ' . $recibo[0]->num_comp_pago;
                    break;
                case '3':
                    $i->medio_de_pago = 'Tarj. de Crédito - Comprobante N°: ' . $recibo[0]->num_comp_pago;
                    break;
                case '4':
                    $i->medio_de_pago = 'Transferencia Bancaria - Comprobante N°: ' . $recibo[0]->num_comp_pago;
                    break;
                case '5':
                    $cheque = Cheque::join('bancos as b', 'b.id', 'cheques.banco_id')
                        ->where('cheques.numero', $i->num_comp_pago)
                        ->select('b.descripcion', 'b.sucursal')->get();
                    $i->medio_de_pago = 'Cheque Banco ' . $cheque[0]->descripcion . '/' . $cheque[0]->sucursal . 
                        ' - N° ' . $recibo[0]->num_comp_pago;
                    break;
                default:
            }            
        }
        $total_pagos_a_cuenta = $total_pagos;
        //paso el total del recibo de números a letras
        $total_recibo = $info_factura[0]->importe;
        $desc_moneda = 'pesos'; 
        $sep = 'con';
        $desc_decimal = 'centavos';

        $arr = explode(".", $total_recibo);
        $entero = $arr[0];
        if (isset($arr[1])) {
            $decimos = strlen($arr[1]) == 1 ? $arr[1] . '0' : $arr[1];
        }        
        $fmt = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);
        if (is_array($arr)) {
            $num_word = ($arr[0]>=1000000) ? "{$fmt->format($entero)} de $desc_moneda" : "{$fmt->format($entero)} $desc_moneda";
            if (isset($decimos) && $decimos > 0) {
                $num_word .= " $sep {$fmt->format($decimos)} $desc_decimal";
            }
        }
        $numeroEnLetra = $num_word;

        $pdf = PDF::loadView('livewire.pdf.pdfRecibos', 
                            compact('info_factura', 'info_recibo', 'numeroEnLetra', 'total_comprobantes', 
                                    'total_recibo', 'total_pagos_a_cuenta', 'mas_de_un_pago_a_cuenta'));
        return $pdf->stream('recibos.pdf');
    }
    public function PDFFactDel($id) 
    {
        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');
        $this->modComandas = session('modComandas');

        $leyenda_factura = Comercio::select('leyenda_factura', 'imp_por_hoja', 'imp_duplicado')
            ->where('id', $this->comercioId)->get();
        
        $leyendaFactura = $leyenda_factura[0]->leyenda_factura;
        $impPorHoja     = $leyenda_factura[0]->imp_por_hoja;
        $impDuplicado   = $leyenda_factura[0]->imp_duplicado;

        $infoDetalle = Detfactura::select('*')->where('comercio_id', $this->comercioId)->get();
        if($infoDetalle->count() > 0){ 
            $infoDetalle = Detfactura::join('facturas as r','r.id','detfacturas.factura_id')
                ->select('detfacturas.*', DB::RAW("'' as p_id"), 
                    DB::RAW("'' as codigo"), DB::RAW("'' as producto"), DB::RAW("'' as es_producto"))
                ->where('detfacturas.factura_id', $id)
                ->where('detfacturas.comercio_id', $this->comercioId)
                ->orderBy('detfacturas.id')->get();  
            $this->total = 0;
            $this->contador_filas = 0;
            foreach ($infoDetalle as $i){
                $this->contador_filas ++;
                $i->importe=$i->cantidad * $i->precio;
                $this->total += $i->importe;
                if($i->producto_id){
                    $producto = Producto::find($i->producto_id);
                    $i->p_id        = $producto->id;
                    $i->codigo      = $producto->codigo;
                    $i->producto    = $producto->descripcion;
                    $i->es_producto = 1;
                }else{
                    $subproducto = Subproducto::find($i->subproducto_id);
                    $i->p_id        = $subproducto->id;
                    $i->codigo      = $subproducto->id;
                    $i->producto    = $subproducto->descripcion;
                    $i->es_producto = 0;             
                }
            }
        } 

        $cliente = false;
        $repartidor = false;
        $mozo = '';
        $mesa = '';
        if($this->modComandas){
            $info = Factura::select('numero','importe','cliente_id','repartidor_id','created_at', 
                'mesa_id', 'mozo_id', DB::RAW("'' as nomCli"),DB::RAW("'' as apeCli"),
                DB::RAW("'' as calleCli"), DB::RAW("'' as numCli"),DB::RAW("'' as localidad"))
                ->where('id',$id)->get();
            if($info[0]->mesa_id){
                $mozo = $info[0]->mozo_id;
                $mesaId = Mesa::find($info[0]->mesa_id); 
                $mesa = $mesaId->descripcion; 
            }         
        }else{
            $info = Factura::select('numero','importe','cliente_id','repartidor_id','created_at', 
                DB::RAW("'' as nomCli"),DB::RAW("'' as apeCli"),
                DB::RAW("'' as calleCli"), DB::RAW("'' as numCli"),DB::RAW("'' as localidad"))
                ->where('id',$id)->get();
        }       
                
        foreach ($info as $i)
        {
            if($i->cliente_id){
                $cli = Cliente::find($i->cliente_id);
                $i->nomCli    = $cli->nombre;
                $i->apeCli    = $cli->apellido;
                $i->calleCli  = $cli->calle;
                $i->numCli    = $cli->numero;
                $loc          = Localidad::find($cli->localidad_id);
                $i->localidad = $loc->descripcion;
                $cliente      = true;  
            }
            if($i->repartidor_id){
                $repartidor = true;
            }
        }
        $pdf = PDF::loadView('livewire.pdf.pdfFactDel', compact([
            'infoDetalle','info','cliente','repartidor','leyendaFactura', 
            'impPorHoja' ,'impDuplicado', 'mesa', 'mozo']));
        return $pdf->stream('fact.pdf');
    }
    public function PDFRemito($id) 
    {
        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');
      
        $infoDetalle = DetRemito::join('productos as p','p.id','det_remitos.producto_id')
            ->where('det_remitos.remito_id', $id)
            ->where('det_remitos.comercio_id', $this->comercioId)
            ->select('det_remitos.*', 'p.codigo', 'p.descripcion as producto')
            ->orderBy('det_remitos.id')->get(); 
        $info = Remito::join('clientes as c','c.id','remitos.cliente_id')
            ->join('users as u','u.id','remitos.repartidor_id')
            ->join('localidades as l','l.id','c.localidad_id')
            ->select('remitos.*', 'remitos.id as id', 'c.nombre as nomCli', 'c.apellido as apeCli', 
                     'c.calle as calleCli', 'c.numero as numCli', 'l.descripcion')
            ->where('remitos.id','like',$id)->get();

        if($info[0]->nomCli == null) {
            $delivery = false;
        }else {              
            $delivery = true;
        }
        $pdf = PDF::loadView('livewire.pdf.pdfRemito', compact([
                             'infoDetalle','info','delivery']));
        return $pdf->stream('fact.pdf');
        
       
    }
    public function PDFViandas() 
    {
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
                                 'c.apellido', 'c.nombre', 'viandas.comentarios')->orderBy('viandas.h_lunes_m')->get();                
            break;
        case '2':
                $info = Vianda::join('clientes as c', 'c.id', 'viandas.cliente_id')
                        ->where('viandas.c_martes_m', '<>', '')
                        ->where('c.comercio_id', $this->comercioId)
                        ->select('viandas.c_martes_m as cantidad','viandas.h_martes_m as hora',
                                 'c.apellido', 'c.nombre', 'viandas.comentarios')->orderBy('viandas.h_martes_m')->get(); 
            break;
        case '3':
                $info = Vianda::join('clientes as c', 'c.id', 'viandas.cliente_id')
                        ->where('viandas.c_miercoles_m', '<>', '')
                        ->where('c.comercio_id', $this->comercioId)
                        ->select('viandas.c_miercoles_m as cantidad','viandas.h_miercoles_m as hora',
                                 'c.apellido', 'c.nombre', 'viandas.comentarios')->orderBy('viandas.h_miercoles_m')->get(); 
            break;
        case '4':
                $info = Vianda::join('clientes as c', 'c.id', 'viandas.cliente_id')
                        ->where('viandas.c_jueves_m', '<>', '')
                        ->where('c.comercio_id', $this->comercioId)
                        ->select('viandas.c_jueves_m as cantidad','viandas.h_jueves_m as hora',
                                 'c.apellido', 'c.nombre', 'viandas.comentarios')->orderBy('h_jueves_m')->get(); 
            break;
        case '5':
                $info = Vianda::join('clientes as c', 'c.id', 'viandas.cliente_id')
                        ->where('viandas.c_viernes_m', '<>', '')
                        ->where('c.comercio_id', $this->comercioId)
                        ->select('viandas.c_viernes_m as cantidad','viandas.h_viernes_m as hora', 
                                 'c.apellido', 'c.nombre', 'viandas.comentarios')->orderBy('viandas.h_viernes_m')->get(); 
            break;
        case '6':
                $info = Vianda::join('clientes as c', 'c.id', 'viandas.cliente_id')
                        ->where('c.vianda', '1')
                        ->where('viandas.c_sabado_m', '<>', '')
                        ->where('c.comercio_id', $this->comercioId)
                        ->select('viandas.c_sabado_m as cantidad','viandas.h_sabado_m as hora',
                                'c.apellido', 'c.nombre', 'viandas.comentarios')->orderBy('viandas.h_sabado_m')->get();       
            break;
        case '7':
                $info = Vianda::join('clientes as c', 'c.id', 'viandas.cliente_id')
                        ->where('viandas.c_domingo_m', '<>', '')
                        ->where('c.comercio_id', $this->comercioId)
                        ->select('viandas.c_domingo_m as cantidad','viandas.h_domingo_m as hora',
                                 'c.apellido', 'c.nombre', 'viandas.comentarios')->orderBy('viandas.h_domingo_m')->get(); 
            break;
            default:
        }
        $pdf = PDF::loadView('livewire.pdf.pdfViandas', compact('info'));
        return $pdf->stream('viandas.pdf');
    }
    public function PDFListadoCtaCte()
    {
        $this->comercioId = session('idComercio');
        //busco los clientes con saldo > 0 
        $info = Ctacte::join('clientes as c', 'c.id', 'cta_cte.cliente_id')
            ->where('c.comercio_id', $this->comercioId)
            ->where('c.saldo', '1')
            ->select('cta_cte.cliente_id', 'c.nombre', 'c.apellido', DB::RAW("'' as importe"))
            ->groupBy('cta_cte.cliente_id', 'c.nombre', 'c.apellido')
            ->orderBy('c.apellido')->orderBy('c.nombre')->get();
        $suma = 0;
        foreach($info as $i) {
            $sumaDebitos=0;
            $sumaRecibos=0;
                //busco facturas impagas y recibos a cuenta de cada cliente en ctacte
            $registroCtaCte = Ctacte::where('cta_cte.cliente_id', $i->cliente_id)
                ->where('estado', '1')->get();
            foreach($registroCtaCte as $r) {   
                if($r->factura_id != null) {    //si es factura las voy sumando
                    $importe = Factura::where('id', $r->factura_id)
                        ->where('estado', 'ctacte')
                        ->where('estado_pago', '0')
                        ->orWhere('id', $r->factura_id)
                        ->where('estado', 'ctacte')
                        ->where('estado_pago', '2')
                        ->select('numero', 'importe')->get();
                    foreach($importe as $imp){
                        $sumaDebitos += $imp->importe; //calculo el total de las facturas de cada cliente
                    }
                }else {                         //busco todos los recibos
                    $importe = Recibo::join('det_metodo_pagos as det', 'det.recibo_id', 'recibos.id')
                        ->where('recibos.id', $r->recibo_id)
                        ->where('recibos.entrega', '1')
                        ->select('recibos.id', 'det.importe')->get();
                    foreach($importe as $imp){
                        $verEstadoPagoFactura = ReciboFactura::join('facturas as f','f.id','recibo_facturas.factura_id')
                            ->where('recibo_facturas.recibo_id',$imp->id)
                            ->where('f.estado_pago','2')->get('recibo_facturas.id');
                        if($verEstadoPagoFactura->count() > 0){
                            $sumaRecibos += $imp->importe; //calculo el total de recibos de cada cliente
                        }
                    }
                }
            }
            //dd($sumaDebitos, $sumaRecibos);
            // calculo el total para cada cliente
            $i->importe = $sumaDebitos - $sumaRecibos;
            // calculo el importe del total gral
            $suma += $i->importe;
        }
        $pdf = PDF::loadView('livewire.pdf.pdfListadoCtaCte', compact('info','suma'));
        return $pdf->stream();  
    }
    public function PDFListadoCtaCteClub()
    {
        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');

        $info = CtaCteClub::join('socios as c', 'c.id', 'cta_cte_clubs.socio_id')
            ->where('c.comercio_id', $this->comercioId)
            ->where('c.saldo', '1')
            ->select('cta_cte_clubs.socio_id', 'c.nombre', 'c.apellido', DB::RAW("'' as importe"))
            ->groupBy('cta_cte_clubs.socio_id', 'c.nombre', 'c.apellido')
            ->orderBy('c.apellido')->orderBy('c.nombre')->get();
        $suma=0;
        foreach($info as $i) {
            $sumaDebitos=0;
            $sumaRecibos=0;
            //verifico si el registro es una factura o es un recibo
            $registroCtaCte = CtaCteClub::where('cta_cte_clubs.socio_id', $i->socio_id)->get();
            foreach($registroCtaCte as $r) {   
                if($r->debito_id != null) {    //si es factura las voy sumando
                    $importe = Debito::where('id', $r->debito_id)
                        ->where('estado', 'ctacte')
                        ->where('estado_pago', '0')
                        ->orWhere('id', $r->debito_id)
                        ->where('estado', 'ctacte')
                        ->where('estado_pago', '2')
                        ->select('importe')->get();
                    foreach($importe as $imp){
                           $sumaDebitos += $imp->importe; //calculo el total de los débitos de cada socio
                    }
                }else { 
                    if($sumaDebitos != 0){
                        $importe = Recibo::where('id', $r->recibo_id)   //busco todos los recibos
                            ->where('entrega', 1)
                            ->select('importe')->get();
                        foreach($importe as $imp){
                            $sumaRecibos += $imp->importe; //calculo el total de recibos de cada socio
                        }
                    }                        
                }
            }
            // calculo el total para cada socio
            $i->importe = $sumaDebitos - $sumaRecibos;
            // solo calculo el importe del total gral si se están mostrando todos los socios
            $suma += $i->importe;
        }  
        $pdf = PDF::loadView('livewire.pdf.pdfListadoCtaCteClub', compact('info','suma'));
        return $pdf->stream('listactacte.pdf');  
    }
    public function PDFResumenDeCuenta($cliId)
    {
        $importeEntrega =0;
        $info = Ctacte::join('clientes as c', 'c.id', 'cta_cte.cliente_id')
            ->join('facturas as f', 'f.id', 'cta_cte.factura_id')
            ->join('localidades as l', 'l.id', 'c.localidad_id')
            ->where('cta_cte.cliente_id', $cliId)
            ->where('f.estado', 'ctacte')
            ->where('f.estado_pago', '0')
            ->orWhere('cta_cte.cliente_id', $cliId)
            ->where('f.estado', 'ctacte')
            ->where('f.estado_pago', '2')
            ->select('cta_cte.factura_id', 'cta_cte.recibo_id', 'cta_cte.cliente_id', 
                    'c.nombre', 'c.apellido', 'c.calle', 'c.numero', 'l.descripcion as localidad',
                    DB::RAW("'' as fecha"), DB::RAW("'' as numero") , DB::RAW("'' as importe"), 
                    DB::RAW("'' as importe_factura"), DB::RAW("'' as resto"))
            ->orderBy('cta_cte.created_at')->get();

        $infoEntrega = Ctacte::join('recibos as r', 'r.id', 'cta_cte.recibo_id')
            ->join('det_metodo_pagos as det', 'det.recibo_id', 'cta_cte.recibo_id')
            ->where('cta_cte.cliente_id', $cliId)
            ->where('r.entrega', '1')
            ->select('r.id','det.importe')->get();
        foreach($infoEntrega as $i){
            $verEstadoPagoFactura = ReciboFactura::join('facturas as f','f.id','recibo_facturas.factura_id')
                ->where('recibo_facturas.recibo_id', $i->id)
                ->where('f.estado_pago','2')->get();
            if($verEstadoPagoFactura->count() > 0)
            $importeEntrega += $i->importe; 
        }  

        $sumaDebitos=0;
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
            $sumaDebitos += $importe[0]->importe; //calculo el total de las facturas de cada cliente
            if($importe[0]->estado_pago == 0) $i->importe_factura = 1; //aviso de factura para pintar rojo   
            else $i->importe_factura = 2;  //aviso de factura para pintar rojo/negrita            
        
            $i->fecha = $importe[0]->created_at;                        
            $i->numero = $importe[0]->numero;                        
            $i->importe = $importe[0]->importe;
            //recupero las entregas y calculo el resto las facturas que correspondan
            if($importe[0]->estado_pago == 2){
                $entregas = 0;
                $pagos = ReciboFactura::join('recibos as r', 'r.id', 'recibo_facturas.recibo_id')
                    ->join('det_metodo_pagos as det', 'det.recibo_id', 'recibo_facturas.recibo_id')
                    ->where('recibo_facturas.factura_id', $i->factura_id)
                    ->select('det.importe')->get();
                foreach($pagos as $p){
                    $entregas += $p->importe;
                }
                $i->resto = $i->importe - $entregas;
            }
        } 
        $totalCli = $sumaDebitos;
        //calculo el saldo del cliente seleccionado
        $saldo = $totalCli - $importeEntrega;
        $pdf = PDF::loadView('livewire.pdf.pdfResumenDeCuenta', compact('info','importeEntrega','totalCli','saldo'));
        return $pdf->stream();
    }
    public function PDFListaDePrecios($numero)
    {
		$this->comercioId = session('idComercio');
        
        $comercio = Comercio::where('id', $this->comercioId)->get();
        if($comercio->count()){  
            $nombre = $comercio[0]->nombre;
            $logo   = $comercio[0]->logo;
        }
		$listaNumero = $numero;

        if($numero == 1){
            $info = Producto::select('codigo', 'descripcion', 'precio_venta_l1 as precio')
            ->where('comercio_id', $this->comercioId)
            ->where('tipo', 'like', 'Art. Compra/Venta')
            ->orWhere('comercio_id', $this->comercioId)
            ->where('tipo', 'like', 'Art. Venta c/receta')
            ->orderBy('codigo')
            ->get();
        }else{
            $info = Producto::select('codigo', 'descripcion', 'precio_venta_l2 as precio')
            ->where('comercio_id', $this->comercioId)
            ->where('tipo', 'like', 'Art. Compra/Venta')
            ->orWhere('comercio_id', $this->comercioId)
            ->where('tipo', 'like', 'Art. Venta c/receta')
            ->orderBy('codigo')
            ->get();
        }
        $pdf = PDF::loadView('livewire.pdf.pdfListaDePrecios', compact('info', 'listaNumero', 'nombre', 'logo'));
        return $pdf->stream();
    }
    public function PDFpedidos2($pedidoId)
    {
        $this->comercioId = session('idComercio');
        $info = Pedido::join('proveedores as p', 'p.id', 'pedidos.proveedor_id')
            ->where('pedidos.estado', 'cargado')
            ->where('pedidos.comercio_id', $this->comercioId)
            ->select('pedidos.id', 'p.nombre_empresa')->orderBy('pedidos.updated_at', 'desc')->get();
   
        foreach($info as $i) { 
            $infoDetPedido = Detpedido::join('productos as p','p.id','detpedidos.producto_id')
                ->join('pedidos as pe', 'pe.id', 'detpedidos.pedido_id')
                ->join('proveedores as pr', 'pr.id', 'pe.proveedor_id')
                ->where('pe.id', $i->id)
                ->where('pe.comercio_id', $this->comercioId)
                ->select('detpedidos.id','detpedidos.cantidad', 'p.descripcion as producto', 
                    'p.precio_costo', 'pr.nombre_empresa')
                ->orderBy('detpedidos.id')->get(); 
        }  
        $pdf = PDF::loadView('livewire.pdf.pdfpedidos', compact('info','infoDetPedido'));
        return $pdf->stream();
         
      
    }
    public function PDFpedidos($pedidoId)
    {
        $this->comercioId = session('idComercio');
        $info = Pedido::join('proveedores as p', 'p.id', 'pedidos.proveedor_id')
            ->where('pedidos.estado', 'cargado')
            ->where('pedidos.comercio_id', $this->comercioId)
            ->select('pedidos.*', 'p.nombre_empresa')->orderBy('pedidos.updated_at', 'desc')->get();
        
        $infoDetPedido = Detpedido::join('productos as p','p.id','detpedidos.producto_id')
            ->join('pedidos as pe','pe.id','detpedidos.pedido_id')
            ->join('proveedores as pr','pr.id','pe.proveedor_id')
            ->where('detpedidos.pedido_id', $pedidoId)
            ->select('detpedidos.*', 'p.descripcion as producto', 'p.precio_costo', 
                'pr.nombre_empresa', DB::RAW("'' as importe"))
            ->orderBy('detpedidos.id')->get(); 

        $empresa = $infoDetPedido[0]->nombre_empresa;
        $total = 0;
        foreach ($infoDetPedido as $i){
            $i->importe=$i->cantidad * $i->precio_costo;
            $total += $i->importe;
        }
        $pdf = PDF::loadView('livewire.pdf.pdfpedidos', compact('info', 'infoDetPedido', 'empresa', 'total'));
        return $pdf->stream();
    }
}
